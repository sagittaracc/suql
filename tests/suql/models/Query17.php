<?php

namespace test\suql\models;

use test\suql\models\tables\TestTable;

# [Table(name="view_17")]
class Query17 extends TestTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function query()
    {
        return 'query_17';
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
}
