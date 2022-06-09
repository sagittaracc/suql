<?php

namespace test\suql\connections;

use suql\db\Container;

trait TestConnection
{
    public function getDb()
    {
        return Container::get('db_test');
    }
}