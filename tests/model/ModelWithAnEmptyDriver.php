<?php

namespace app\models;

class ModelWithAnEmptyDriver extends \SuQLTable
{
    protected $driver = null;

    public function table()
    {
        return 'table';
    }

    public function relations()
    {
        return [];
    }
}