<?php

namespace test\suql\models;

use suql\syntax\entity\SuQLTable;

class Query1 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function table()
    {
        return 'table_1';
    }
}