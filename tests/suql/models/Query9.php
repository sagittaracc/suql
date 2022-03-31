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
        return $this
            ->column('p1')
                ->setType('int')
                ->setLength(11)
                ->setDefault(0)
            ->column('p2')
                ->setType('varchar')
                ->setLength(255)
                ->setDefault('');
    }

    public function getDb()
    {
        return Container::get('db_test');
    }
}
