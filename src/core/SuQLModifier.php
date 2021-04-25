<?php

namespace suql\core;

class SuQLModifier
{
    private $modifier;
    private $params;
    private $field;

    function __construct($modifier, $params = [])
    {
        $this->modifier = $modifier;
        $this->params = $params;
    }

    public function getModifier()
    {
        return $this->modifier;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function applyTo($field)
    {
        $this->field = $field;
        return $this;
    }

    public function getField()
    {
        return $this->field;
    }
}