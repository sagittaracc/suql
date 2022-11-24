<?php

namespace suql\core\where;

class Raw
{
    private $where;

    function __construct($where)
    {
        $this->where = $where;
    }

    public function getWhere()
    {
        return $this->where;
    }
}