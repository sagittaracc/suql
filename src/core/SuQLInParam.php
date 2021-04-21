<?php

namespace core;

class SuQLInParam extends SuQLParam
{
    public function getValue()
    {
        return '(' . implode(',', $this->params) . ')';
    }

    public function getPlaceholder()
    {
        $placeholder = parent::getPlaceholder();
        $paramList = [];
        foreach ($this->params as $paramId => $param)
        {
            $paramList[] = ':ph_' . md5($placeholder . $paramId);
        }
        return '(' . implode(',', $paramList) . ')';
    }
}