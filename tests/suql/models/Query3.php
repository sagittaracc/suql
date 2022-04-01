<?php

namespace test\suql\models;

use suql\db\Container;
use test\suql\records\ActiveRecord;

class Query3 extends ActiveRecord
{
    public function table()
    {
        return 'table_3';
    }

    public function getDb()
    {
        return Container::get('db_test');
    }
}