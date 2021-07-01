<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;
use suql\db\Container;

class TableNameWithFields extends ActiveRecord
{
    public $field;

    public function table()
    {
        return 'table_name';
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
