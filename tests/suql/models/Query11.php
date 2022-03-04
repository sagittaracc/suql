<?php

namespace test\suql\models;

use suql\db\Container;
use test\suql\records\ActiveRecord;

class Query11 extends ActiveRecord
{
    public $f1;
    public $f2;

    public function table()
    {
        return 'table_10';
    }

    public function fields()
    {
        return [];
    }

    public function getDb()
    {
        return Container::get('db_test');
    }
}
