<?php

namespace app\models;

class ModelWithAnUnsupportedDriver extends \SuQLTable
{
    protected $dbms = 'oracle';

    public function table()
    {
        return 'table';
    }

    public function relations()
    {
        return [];
    }
}