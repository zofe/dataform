<?php

namespace Zofe\DataForm\Fields;

use Zofe\DataForm\Form;

class Field
{
    public $type = "field";
    public $multiple = false;
    public $visible = true;
    public $status = "edit";

    public $name;
    public $label;
    public $value = null;
    public $rule = '';
    public $req = '';
    public $messages = array();
    public $attributes = array();
    public $output = '';
    public $has_error = '';
    public $request_refill = true;

    /**
     * set value (override it in fields to format) 
     * 
     * @param $value
     */
    public function setValue($value) {
        $this->value = $value;
    }

    /**
     * get value
     * 
     * @return null
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * add rules for field es.:  required|min:5 ...
     * @param $rule
     * @return $this
     */
    public function rule($rule)
    {
        $this->rule = trim($this->rule."|".$rule, "|");
        if ((strpos($this->rule, "required") !== false) and !isset($this->no_star)) {
            $this->required = true;
        }

        return $this;
    }
    
    /**
     * display field on "edit" status
     * 
     * @return string
     */
    public function edit() {
        return Form::text($this->name, $this->value, $this->attributes);
    }

    /**
     * display field on "hide" status
     * 
     * @return string
     */
    public function hide() {
        return  Form::hidden($this->name, $this->value);
    }

    /**
     * display value on "show" status
     * 
     * @return mixed
     */
    public function show() {
        return $this->value;
    }
    

    
    public function build() {
        
        if (($this->status == "hidden" || $this->visible === false || in_array($this->type, array("hidden", "auto")))) {
            $this->is_hidden = true;
        }
        $this->message = implode("<br />\n", $this->messages);


        if ($this->orientation == 'inline') {
            $this->attributes["placeholder"] = $this->label;
        }

        if ($this->visible === false) {
            return false;
        }
    }
}