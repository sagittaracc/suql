<?php

namespace test\suql\models;

use suql\db\Container;
use tests\suql\entity\SuQLTable;

class Query16 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function query()
    {
        return 'query_16';
    }

    public function table()
    {
        return 'table_16';
    }

    public function view()
    {
        return <<<SQL
            select
                *
            from table_16
            where f1 % 2 = 0
SQL;
    }

    public function getDb()
    {
        return Container::get('db_test');
    }
}
