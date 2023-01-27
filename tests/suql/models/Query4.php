<?php

namespace test\suql\models;

use suql\annotation\attributes\Table;
use suql\builder\MySQLBuilder;
use suql\syntax\entity\SuQLTable;
use test\suql\schema\AppScheme;

#[Table(name: "table_4", alias: "t4")]
class Query4 extends SuQLTable
{
    protected static $schemeClass = AppScheme::class;
    protected static $builderClass = MySQLBuilder::class;

    public function fields()
    {
        return [
            'f1',
            'f2',
            'f3',
        ];
    }
}