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
        $instance->addSelect($instance->query());
        return $instance;
    }
    /**
     * @inheritdoc
     */
    public function join($option, $type = 'inner', $algorithm = 'simple', $on = '')
    {
        if (class_exists($option) && is_subclass_of($option, SuQL::class)) {
            $model = $option::all();
            // $this->getSelect($this->query())->addJoin('data', 'tmp');
        }

        return $this;
    }
    /**
     * @inheritdoc
     */
    public function fetch($method)
    {
        // var_dump($this->getSelect($this->query())->getJoin());
        return $this->data();
    }
}
