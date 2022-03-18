<?php

namespace test\suql\models;

use suql\db\Container;
use test\suql\records\ActiveRecord;

class Query20 extends ActiveRecord
{
    public function table()
    {
        return 'table_20';
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
