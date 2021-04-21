<?php

namespace core;

class SuQLParam
{
    protected $field;
    protected $params;

    function __construct($field, $params)
    {
        $this->field = $field;
        $this->params = $params;
    }

    public function getField()
    {
        return $this->field;
    }

    public function getValue()
    {
        return $this->params[0];
    }

    public function getPlaceholderName()
    {
      return ':ph_' . md5($this->field->getField());
    }
}