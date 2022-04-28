<?php

namespace test\suql\models;

use tests\suql\entity\SuQLTable;

class Query21 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\UndefinedSchema';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function table()
    {
        return 'table_1';
    }
}