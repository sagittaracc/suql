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

    public function getPlaceholder()
    {
      return $this->getPlaceholderList()[0];
    }

    private function getPlaceholderList()
    {
        return [
            ':ph_' . md5($this->field->getField())
        ];
    }
}