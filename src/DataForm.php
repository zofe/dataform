<?php

namespace Zofe\DataForm;


use Zofe\Burp\BurpEvent;
use Zofe\DataForm\Fields\Field;

class DataForm
{
    public $model;
    public $fields = array();
    public $multipart = false;
    public $output = '';
    public $validator;

    public $process_status = "idle";
    public $status = "edit";
    public $action = "idle";

    public $open;
    public $close;
    public $label;
    public $button_container = array( "TR"=>array(), "BL"=>array(), "BR"=>array() );
    public $message = "";
    public $rules = "";
    public $error = "";
    protected $method = 'POST';
    protected $redirect = null;
    protected $process_url = '';
    protected $orientation = 'horizontal';
    protected $form_callable = false;
    protected $attributes = array('class' => "form-horizontal", 'role' => "form", 'method' => 'POST');
    

    /**
     * Main method, set source (model) or create an empty form
     *
     * @param $source
     * @return static
     */
    public static function create($source = null)
    {
        $ins = new static();
        $ins->process_url = link_route('save');
        if (is_object($source) && is_a($source, '\Illuminate\Database\Eloquent\Model')) {
            $ins->model = $source;
            $ins->status = ($ins->model->exists) ? "modify" : "create";
        }
        BurpEvent::listen('dataform.save', array($ins, 'save'));
        return $ins;
    }

    /**
     * alias for create()
     * 
     * @param string $source
     * @return DataForm
     */
    public static function source($source = '')
    {
        return self::create($source);
    }

    /**
     * save data on model or just fill fields values
     *
     * @return bool
     */
    public function save()
    {
        $this->setFieldValues();
        if ($this->isValid()) {
            
            $this->getFieldValues();
            //set field values


            /*if (isset($this->model)) {
                return $this->model->save();
            } else {
                return true;
            }*/
            $this->process_status = "success";
            
            //callable
            if ($this->form_callable) {
                
                $callable = $this->form_callable;
                $result = $callable($this);
                
                //verificare se nella closure c'è un header location 
                //o un redirect di laravel
                if ($result) {
                    //$this->redirect = $result;
                    return $result;
                }

                /*
                if ($result && is_a($result, 'Illuminate\Http\RedirectResponse')) {
                    $this->redirect = $result;
                }
                //reprocess if an error is added in closure
                if ($this->process_status == 'error') {
                    $this->process();
                }*/
            }

            //cleanup submits if success
            if ($this->process_status == 'success') {
                $this->removeFieldType('submit');
            }

        }

        $this->process_status = "error";
        return false;
    }

    /**
     * set field values
     *
     * @return bool
     */
    protected function setFieldValues()
    {

        //prenderli dal model, o dai valori di default se non c'è il post.. 
        foreach ($this->fields as $field)
        {
            
            if ($field->request_refill == true && isset($_POST[$field->name]) ) {
                $field->setValue($_POST[$field->name]);
                $field->is_refill = true;
            }


        }
    }

    /**
     * get field values 
     *
     * @return bool
     */
    protected function getFieldValues()
    {
        foreach ($this->fields as $field)
        {
            $this->values[] = $field->getValue($field->name);
        }
    }

    /**
     * remove field from fields list
     *
     * @param $fieldname
     * @return $this
     */
    public function removeField($fieldname)
    {
        if (isset($this->fields[$fieldname]))
            unset($this->fields[$fieldname]);

        return $this;
    }

    /**
     * remove field where type==$type from field list and button container
     *
     * @param $type
     * @return $this
     */
    public function removeFieldType($type)
    {
        foreach ($this->fields as $fieldname => $field) {
            if ($field->type == $type) {
                unset($this->fields[$fieldname]);
            }
        }
        foreach ($this->button_container as $container => $buttons) {
            foreach ($buttons as $key=>$button) {
                if (strpos($button, 'type="'.$type.'"')!==false) {
                    $this->button_container[$container][$key] = "";
                }
            }
        }

        return $this;
    }
    
    
    /**
     * add field to the form using name, label and type
     * 
     * @param $name
     * @param $label
     * @param $type
     * @return mixed
     */
    public function add($name, $label, $type)
    {
        $classname = '\Zofe\DataForm\Fields\\' . ucfirst($type);
        $legacy = '\Zofe\DataForm\Fields\\' . 'add' . ucfirst($type);

        if (class_exists($classname) || class_exists($legacy)) {
            $class = (class_exists($legacy)) ? $legacy : $classname;

            $field = new $class;
            $field->name = $name;
            $field->label = $label;
            return $this->append($field);
        }
    }


    /**
     * append field to the form using field instance
     *
     * @param string $name
     * @return mixed
     */
    protected function append(Field $field)
    {
        $this->fields[$field->name] = $field;

        if (in_array($field->type, array('file','image'))) {
            $this->multipart = true;
        }
        
        $this->fields[$field->name] = $field;

        return $field;
    }

    /**
     * add a submit button
     *
     * @param string $name
     * @param string $position
     * @param array  $options
     * @return $this
     */
    public function submit($name, $position = "BL", $options = array())
    {
        $options = array_merge(array("class" => "btn btn-primary"), $options);
        $this->button_container[$position][] = Form::submit($name, $options);

        return $this;
    }
    
    /**
     * Magic method to catch all appends using $form->{fieldtype}(...)
     *
     * @param  string $name
     * @param  Array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (count($arguments) === 2) {
            return $this->add($arguments[0], $arguments[1], $name);
        }
    }

    /**
     * field validation, check all validation rules
     *
     * @return bool
     */
    protected function isValid()
    {
        if ($this->error != "") {
            return false;
        }

        $rules = array();
        $messages = array();
        $attributes = array();
        foreach ($this->fields as $field) {
            //$field->action = $this->action;
            if (isset($field->rule)) {
                $rules[$field->name] = $field->rule;
                $messages[$field->name] = null;
                $attributes[$field->name] = $field->label;
            }
        }
        if (!isset($this->validator)) {
            $this->validator = validator($_POST, $rules, $messages, $attributes);
        }
        if (isset($rules)) {

            return !$this->validator->fails();
        } else {
            return true;
        }
    }

    /**
     * build each field and share some data from dataform to field 
     * (form status, validation errors)
     */
    protected function buildFields()
    {
        $messages = (isset($this->validator)) ? $this->validator->messages() : false;
        foreach ($this->fields as $field) {
            $field->status = $this->status;
            $field->orientation = $this->orientation;
            if ($messages and $messages->has($field->name)) {
                $field->messages = $messages->get($field->name);
                $field->has_error = " has-error";
            }
            $field->build();
        }
    }

    /**
     * prepare some var (form open tag, errors, etc) 
     */
    public function prepareForm()
    {
        // Set the form open and close
        if ($this->status == 'show') {
            $this->open = '<div class="form">';
            $this->close = '</div>';
        } else {

            $this->open = Form::open($this->process_url, $this->attributes);
            $this->close = Form::hidden('save', 1) . Form::close();

            if ($this->method == "GET") {
                $this->close = Form::hidden('search', 1) . Form::close();
            }
        }
        if (isset($this->validator)) {
            $this->errors = $this->validator->messages();
            $this->error .=  implode('<br />',$this->errors->all());
        }
    }
    
    public function build($view)
    {
        BurpEvent::flush('dataform.save');

        $view = ($view) ? $view : 'dataform.dataform';
        $this->buildFields();
        $this->prepareForm();
        $this->output = blade($view, array('df'=>$this));
        
        //build each section reparately (for custom forms) 
        $sections = blade($view, array('df'=>$this), null, false)->renderSections();
        $this->header = $sections['df.header'];
        $this->footer = $sections['df.footer'];
        $this->body = @$sections['df.fields'];
    }
    
    public function getForm($view = null)
    {
        $this->build($view);
        return $this->output;
    }

    public function __toString()
    {
        if ($this->output == "") {
            try {
                $this->getForm();
            }
            catch (\Exception $e) {
                return '<div class="alert alert-danger">'.
                $e->getMessage() ."<br>\n".
                "File: <small>".$e->getFile() . "</small><br>\n".
                "Line: " . $e->getLine().'</div>';
            }
        }
        return $this->output;
    }

    /**
     * build form and check if process status is "success"
     * execute a callable
     *
     * @param callable $callable
     */
    public function saved(\Closure $callable)
    {
        $this->form_callable = $callable;
    }

    /**
     * alias for saved
     *
     * @param callable $callable
     */
    public function passed(\Closure $callable)
    {
        $this->saved($callable);
    }

    /**
     * append error (to be used in passed/saved closure)
     *
     * @param string $url
     * @param string $name
     * @param string $position
     * @param array  $attributes
     *
     * @return $this
     */
    public function error($error)
    {
        $this->process_status = 'error';
        $this->message = '';
        $this->error .= $error;

        return $this;
    }

    /**
     * replace form content with a message (error or success)
     * 
     * @param $message
     * @return $this
     */
    public function message($message)
    {
        $this->message =  $message;

        return $this;
    }

    /**
     * @param string $url
     * @param string $name
     * @param string $position
     * @param array  $attributes
     *
     * @return $this
     */
    public function link($url, $name, $position="BL", $attributes=array())
    {
        /*$match_url = trim(parse_url($url, PHP_URL_PATH),'/');
        if (Request::path()!= $match_url) {
            $url = Persistence::get($match_url);
        }*/

        $attributes = array_merge(array("class"=>"btn btn-default"), $attributes);
        $this->button_container[$position][] =  link_url($url, $name, $attributes);
        $this->links[] = $url;

        return $this;
    }

    /**
     * @param string $route
     * @param string $name
     * @param array  $parameters
     * @param string $position
     * @param array  $attributes
     *
     * @return $this
     */
    public function linkRoute($route, $name, $parameters=array(), $position="BL", $attributes=array())
    {
        return $this->link(link_route($route, $parameters), $name, $position, $attributes);
    }
}