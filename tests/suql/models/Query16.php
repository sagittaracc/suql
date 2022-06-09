<?php

namespace test\suql\models;

use test\suql\models\tables\TestTable;

# [Table(name="table_16")]
class Query16 extends TestTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function query()
    {
        return 'query_16';
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
}
