<?php

namespace test\suql\models;

use test\suql\models\tables\TestMySQLTable;
use test\suql\schema\AppScheme;

# [Table(name="table_16")]
class Query16 extends TestMySQLTable
{
    protected static $schemeClass = AppScheme::class;

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
