<?php

namespace test\suql\models;

use suql\syntax\entity\SuQLTable;

class Query6 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function table()
    {
        return Query7::all()->select(['f1', 'f2', 'f3']);
    }

    public function fields()
    {
        return [
            'f1',
            'f2',
        ];
    }
}