<?php

namespace Zofe\DataForm\Fields;

use Zofe\DataForm\Form;

class Textarea extends Field
{
    public $type = "textarea";

    public function edit() {
        return Form::textarea($this->name, $this->value, $this->attributes);
    }

}