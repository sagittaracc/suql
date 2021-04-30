<?php

namespace app\models;

use suql\syntax\SuQLTable;

class ModelWithAnUnsupportedDriver extends SuQLTable
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