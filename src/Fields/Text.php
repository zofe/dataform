<?php

namespace Zofe\DataForm\Fields;

use Zofe\DataForm\Form;

class Text extends Field
{
    public $type = "text";

    public function edit() {
        return Form::text($this->name, $this->value, $this->attributes);
    }
    
    public function build() {
        if (parent::build() === false) return;
        
        $this->output = $this->make($this->status);
    }
    
    protected function make($status)
    {
        return $this->$status();
    }
}