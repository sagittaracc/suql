<?php

namespace test\suql\models;

use suql\db\Container;
use suql\syntax\SuQL;

class Query19 extends SuQL
{
    public $c1;
    public $c2;

    public function table()
    {
        return 'table_19';
    }

    public function create()
    {
        return
            $this->column('c1')
                    ->setType('int')
                    ->setLength(11)
                    ->setDefault(0)
                ->column('c2')
                    ->setType('varchar')
                    ->setLength(255)
                    ->setDefault(null);
                
    }

    public function getDb()
    {
        return Container::get('db_test');
    }
}
