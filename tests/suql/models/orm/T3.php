<?php

namespace test\suql\models;

use suql\db\Container;
use test\suql\records\ActiveRecord;

class T3 extends ActiveRecord
{
    public $c1;
    public $c2;

    public function table()
    {
        return 'ot3';
    }

    public function getDb()
    {
        return Container::get('db_test');
    }
}
