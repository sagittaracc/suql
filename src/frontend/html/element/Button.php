<?php

namespace suql\frontend\html\element;

class Button extends Element
{
    private $caption;

    public function setCaption($caption)
    {
        $this->caption = $caption;
        return $this;
    }

    public function render()
    {
        return '<button '.parent::buildEvents().'>'.$this->caption.'</button>';
    }
}