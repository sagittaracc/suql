<?php

namespace test\suql\models;

use suql\db\Container;
use test\suql\records\ActiveRecord;

class Query9 extends ActiveRecord
{
    public function table()
    {
        return 'table_9';
    }

    public function create()
    {
        return [
            'p1' => 'integer',
            'p2' => 'string',
        ];
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
