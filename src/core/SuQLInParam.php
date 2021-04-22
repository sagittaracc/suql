<?php

namespace core;

class SuQLInParam extends SuQLParam
{
    public function getPlaceholder()
    {
        return '(' . implode(',', $this->getPlaceholderList()) . ')';
    }

    public function getPlaceholderList()
    {
        $list = [];
        $placeholder = parent::getPlaceholderList()[0];
        for ($i = 0, $n = count($this->params); $i < $n; $i++)
        {
            $list[] = ':ph_' . md5($placeholder . $i);
        }
        return $list;
    }

    public function isValuable()
    {
        foreach ($this->params as $param)
        {
            if (is_null($param))
                return false;
        }

        return true;
    }
}