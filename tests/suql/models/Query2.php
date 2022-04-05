<?php

namespace test\suql\models;

use tests\suql\entity\SuQLTable;

class Query2 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function table()
    {
        return 'table_2';
    }
}