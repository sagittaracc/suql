<?php

namespace app\model;

class ModelWithWrongTable extends \PDOSuQLTable
{
    protected $dbname = 'test';

    public function table()
    {
        return 'blabla';
    }

    public function relations()
    {
        return [];
    }
}
