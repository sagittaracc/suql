<?php

namespace test\suql\models;

use suql\db\Container;
use tests\suql\entity\SuQLTable;

class Query18 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function table()
    {
        return 'table_1';
    }

    public function getDb()
    {
        return Container::get('db-sqlite');
    }
}
