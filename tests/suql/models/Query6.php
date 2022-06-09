<?php

namespace test\suql\models;

use suql\builder\MySQLBuilder;
use suql\syntax\entity\SuQLTable;
use test\suql\schema\AppScheme;

class Query6 extends SuQLTable
{
    protected static $schemeClass = AppScheme::class;
    protected static $builderClass = MySQLBuilder::class;

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