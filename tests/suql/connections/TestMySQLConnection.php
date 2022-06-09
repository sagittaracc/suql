<?php

namespace test\suql\connections;

use suql\db\Container;

trait TestMySQLConnection
{
    public function getDb()
    {
        return Container::get('db_test');
    }
}