<?php

namespace test\suql\models;

use suql\db\Container;
use test\suql\records\ActiveRecord;

class T2 extends ActiveRecord
{
    public $b1;
    public $b2;

    public function table()
    {
        return 'ot2';
    }

    public function getDb()
    {
        return Container::get('db_test');
    }
}
