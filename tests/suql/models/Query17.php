<?php

namespace test\suql\models;

use suql\db\Container;
use tests\suql\entity\SuQLTable;

class Query17 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function query()
    {
        return 'query_17';
    }

    public function table()
    {
        return 'view_17';
    }

    public function view()
    {
        return <<<SQL
            select
                f1
            from table_10
            where f1 % 2 = 0
SQL;
    }

    public function getDb()
    {
        return Container::get('db_test');
    }
}
