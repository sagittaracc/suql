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

    //TODO: Возможно paramKey совсем не нужен мы больше не привязываем значения к плейсхолдеру во время fetch
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
            ':ph_' . md5($this->field->getField())
        ];
    }
}