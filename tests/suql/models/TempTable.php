<?php

namespace test\suql\models;

use suql\db\Container;
use test\suql\records\ActiveRecord;

class TempTable extends ActiveRecord
{
    public function table()
    {
        return 'temp_table';
    }

    public function create()
    {
        return [
            'id' => 'integer',
            'name' => 'string',
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
