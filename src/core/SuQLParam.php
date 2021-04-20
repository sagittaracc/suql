<?php

namespace core;

class SuQLParam
{
    protected $field;
    protected $value;

    function __construct($field, $value)
    {
        $this->field = $field;
        $this->value = $value;
    }

    public function getField()
    {
        return $this->field;
    }

    public function getValue()
    {
        return $this->value;
    }
}