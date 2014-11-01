<?php

namespace Zofe\DataForm\Fields;

use Zofe\DataForm\Form;

class Select extends Field
{
    public $type = "checkbox";
    public $description = "";
    public $clause = "where";
    
    public function setValue($value) {
        
        foreach ($this->options as $val => $desc) {
            if ($val == $value) {
                $this->value = $val;
                $this->description = $desc;
            }
        }
    }
    
    public function edit() {
        return Form::select($this->name, $this->options, $this->value, $this->attributes);
    }

}