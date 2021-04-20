<?php

namespace core;

class SuQLParam
{
    protected $param;
    protected $value;

    function __construct($param, $value)
    {
        $this->param = $param;
        $this->value = $value;
    }

    public function getParam()
    {
        return $this->param;
    }

    public function getValue()
    {
        return $this->value;
    }
}