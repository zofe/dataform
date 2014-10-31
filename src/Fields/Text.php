<?php

namespace Zofe\DataForm\Fields;

use Zofe\DataForm\Form;

class Text extends Field
{
    public $type = "text";

    public function edit() {
        return Form::text($this->name, $this->value, $this->attributes);
    }

}