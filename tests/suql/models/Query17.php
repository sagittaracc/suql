<?php

namespace test\suql\models;

use suql\annotation\attributes\Table;
use test\suql\models\tables\TestMySQLTable;

#[Table(name: "view_17")]
class Query17 extends TestMySQLTable
{
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
