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

    public function isValuable()
    {
        return !is_null($this->params[0]);
    }

    public function getParamKey()
    {
        return 'pk_' . md5($this->field->getField());
    }

    public function getParamList()
    {
        return array_combine($this->getPlaceholderList(), $this->params);
    }

    public function getPlaceholder()
    {
      return $this->getPlaceholderList()[0];
    }

    public function getPlaceholderList()
    {
        return [
            $this->params[0] instanceof SuQLPlaceholder ? $this->params[0] : ':ph_' . md5($this->field->getField())
        ];
    }
}