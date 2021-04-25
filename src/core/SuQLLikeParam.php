<?php

namespace suql\core;

class SuQLLikeParam extends SuQLParam
{
    public function getParamList()
    {
        $paramList = parent::getParamList();

        foreach ($paramList as $placeholder => &$value)
        {
            $paramList[$placeholder] = "%{$value}%";
        }
        unset($value);

        return $paramList;
    }
}