<?php

namespace test\suql\models;

use suql\syntax\entity\SuQLTable;

# [Table(name="table_4", alias="t4")]
class Query4 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function fields()
    {
        return [
            'f1',
            'f2',
            'f3',
        ];
    }
}