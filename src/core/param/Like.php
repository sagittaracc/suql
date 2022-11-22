<?php

namespace suql\core\param;

/**
 * Параметр типа like
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Like extends Simple
{
    /**
     * {@inheritdoc}
     */
    public function getParamList()
    {
        $paramList = parent::getParamList();

        foreach ($paramList as $placeholder => $value) {
            $paramList[$placeholder] = "%{$value}%";
        }

        return $paramList;
    }
}
