<?php

namespace test\suql\models;

use suql\db\Container;
use test\suql\records\ActiveRecord;

class T1 extends ActiveRecord
{
    public $a1;
    public $a2;

    public function table()
    {
        return 'ot1';
    }

    public function getDb()
    {
        return Container::get('db_test');
    }
}
