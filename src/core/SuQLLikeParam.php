<?php

namespace suql\core;

/**
 * Параметр типа like
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SuQLLikeParam extends SuQLSimpleParam
{
    /**
     * {@inheritdoc}
     */
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
