<?php

namespace test\suql\models;

use suql\db\Container;
use suql\syntax\entity\SuQLTable;

# [Table(name="table_26")]
class Query26 extends SuQLTable
{
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function getDb()
    {
        return Container::get('db_test');
    }
}