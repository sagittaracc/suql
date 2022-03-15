<?php

namespace test\suql\models;

use suql\db\Container;
use test\suql\records\ActiveRecord;

class Query18 extends ActiveRecord
{
    public function table()
    {
        return 'table_1';
    }

    public function create()
    {
        return [];
    }

    public function fields()
    {
        return [];
    }

    public function getDb()
    {
        return Container::get('db-sqlite');
    }
}
