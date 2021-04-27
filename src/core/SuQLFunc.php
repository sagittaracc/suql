<?php

namespace suql\core;

class SuQLFunc extends SuQLQuery implements FunctionQueryInterface
{
    private $params;

    public function addParams($params)
    {
        $this->params = $params;
    }

    public function getName()
    {
        return $this->osuql->getName();
    }

    public function getParams()
    {
        return $this->params;
    }
}