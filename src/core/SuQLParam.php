<?php

namespace suql\core;

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

    public function getFieldHash()
    {
        return md5($this->field->getField());
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getParamKey()
    {
        return "pk_{$this->getFieldHash()}";
    }

    public function getParamList()
    {
        return array_combine($this->getPlaceholderList(), $this->params);
    }

    public function getPlaceholderList()
    {
        $placeholderList = [];

        foreach ($this->params as $index => $param)
        {
            $placeholderList[] = $param instanceof SuQLPlaceholder ? $param->getPlaceholder() : ":ph{$index}_{$this->getFieldHash()}";
        }

        return $placeholderList;
    }

    public function isValuable()
    {
        foreach ($this->params as $param)
        {
            if (is_null($param))
            {
                return false;
            }
        }
        
        return true;
    }
}
