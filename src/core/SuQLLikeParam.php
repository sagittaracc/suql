<?php

namespace suql\core;

class SuQLLikeParam extends SuQLSimpleParam
{
    public function getParamList()
    {
        $paramList = parent::getParamList();

        foreach ($paramList as $placeholder => $value)
        {
            $paramList[$placeholder] = "%{$value}%";
        }

        return $paramList;
    }
}
