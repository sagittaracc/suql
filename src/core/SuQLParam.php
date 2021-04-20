<?php

namespace core;

class SuQLParam
{
    private $param;
    private $value;

    function __construct($param, $value)
    {
        $this->param = $param;
        $this->value = $value;
    }

    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    public function getValue()
    {
        return $this->value;
    }
}