<?php

namespace test\suql\models;

use suql\db\Container;
use test\suql\records\ActiveRecord;

class Query16 extends ActiveRecord
{
    public function query()
    {
        return 'query_16';
    }

    public function table()
    {
        return 'table_16';
    }

    public function fields()
    {
        return [];
    }

    public function real()
    {
        return true;
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
