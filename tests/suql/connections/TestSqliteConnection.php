<?php

namespace test\suql\connections;

use suql\db\Container;

trait TestSqliteConnection
{
    public function getDb()
    {
        return Container::get('db-sqlite');
    }
}