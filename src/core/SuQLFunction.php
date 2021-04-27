<?php

namespace suql\core;

use sagittaracc\PlaceholderHelper;

class SuQLFunction extends SuQLQuery implements FunctionQueryInterface
{
    private $name;
    private $params;

    function __construct($osuql, $name)
    {
        parent::__construct($osuql);
        $this->name = $name;
    }

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
        return $this->name;
    }

    public function getParams()
    {
        return $this->params;
    }
}