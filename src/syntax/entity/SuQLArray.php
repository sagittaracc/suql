<?php

namespace suql\syntax\entity;

use suql\syntax\ArrayInterface;
use suql\syntax\SuQL;

abstract class SuQLArray extends SuQL implements ArrayInterface
{
    /**
     * @inheritdoc
     */
    public static function all()
    {
        return parent::all();
    }
    /**
     * @inheritdoc
     */
    public function join($option, $type = 'inner', $algorithm = 'simple', $on = '')
    {
        return $this;
    }
    /**
     * @inheritdoc
     */
    public function fetch($method)
    {
        return $this->data();
    }
}
