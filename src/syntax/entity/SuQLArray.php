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
        $instance = parent::all();
        return $instance->data();
    }
}
