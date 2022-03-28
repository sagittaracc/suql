<?php

namespace test\suql\models;

use suql\db\Container;
use suql\syntax\SuQL;

class Query18 extends SuQL
{
    public function table()
    {
        return 'table_1';
    }

    public function create()
    {
        return [];
    }

    public function getDb()
    {
        return Container::get('db-sqlite');
    }
}
