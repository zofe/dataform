<?php

namespace Zofe\DataForm;

use Illuminate\Support\Collection;

class FieldCollection extends Collection
{

    public function add($name, $label, $type)
    {
        $classname = '\Zofe\DataForm\Fields\\' . ucfirst($type);
        $legacy = '\Zofe\DataForm\Fields\\' . 'add' . ucfirst($type);
        if (class_exists($classname) || class_exists($legacy)) {
            $class = (class_exists($legacy)) ? $legacy : $classname;

            $field = new $class;
            $field->name = $name;
            $field->label = $label;
            return $this->push($field);

        } else {
            throw new \InvalidArgumentException('\Zofe\DataForm\Fields\Field subclass expected');
        }
        
    }

    /**
     * remove field where type==$type from field list and button container
     *
     * @param $type
     * @return $this
     */
    public function removeType($type)
    {
        $this->filter(function($field) use($type) {
            if ($field->type == $type) $this->forget($field->name);
        });
        return $this;
    }
    
    /**
     * Push an item onto the end of the collection.
     *
     * @param  mixed  $value
     * @return void
     */
    public function push($item)
    {
        $this->items[$item->name] = $item;
        return $item;
    }
    
}
