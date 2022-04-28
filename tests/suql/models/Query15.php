<?php

namespace test\suql\models;

use suql\syntax\entity\SuQLTable;

class Query15 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function query()
    {
        return 'query_15';
    }

    public function table()
    {
        return 'table_15';
    }

    public function view()
    {
        return
            $this
                ->select(['*'])
                ->limit(1);
    }
}