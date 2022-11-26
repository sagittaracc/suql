<?php

namespace suql\frontend\html\element;

class Input extends Element
{
    public static function currentValue()
    {
        return 'this.value';
    }

    public function render()
    {
        return '<input type="text" '.parent::buildEvents().' value="{{'.$this->name.'}}" id="'.$this->id.'">';
    }
}