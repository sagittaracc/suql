<?php

namespace suql\core;

use sagittaracc\PlaceholderHelper;

class SuQLFunc extends SuQLQuery implements FunctionQueryInterface
{
    private $params;

    public function addParams($params)
    {
        foreach ($params as $param)
        {
            if ($param instanceof SuQLPlaceholder)
            {
                $this->params[] = $param->getPlaceholder();
            }
            else
            {
                $this->params[] = (new PlaceholderHelper("?"))->bind($param);
            }
        }
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