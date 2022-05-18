<?php

namespace test\suql\models;

use suql\syntax\entity\SuQLTable;

class Query23 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function query()
    {
        return 'query_13';
    }

    public function table()
    {
        return 'table_13';
    }
}