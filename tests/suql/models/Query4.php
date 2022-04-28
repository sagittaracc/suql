<?php

namespace test\suql\models;

use suql\syntax\entity\SuQLTable;

class Query4 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function table()
    {
        return ['table_4' => 't4'];
    }

    public function fields()
    {
        return [
            'f1',
            'f2',
            'f3',
        ];
    }
}