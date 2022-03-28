<?php

namespace test\suql\models;

use suql\db\Container;
use test\suql\records\ActiveRecord;

class Query17 extends ActiveRecord
{
    public function query()
    {
        return 'query_17';
    }

    public function table()
    {
        return 'view_17';
    }

    public function real()
    {
        return true;
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
