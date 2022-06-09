<?php

namespace test\suql\models;

use suql\builder\MySQLBuilder;
use suql\syntax\entity\SuQLTable;
use suql\syntax\field\Raw;
use test\suql\schema\AppScheme;

class Query5 extends SuQLTable
{
    protected static $schemeClass = AppScheme::class;
    protected static $builderClass = MySQLBuilder::class;

    public function view()
    {
        return $this->select([
            Raw::expression('2 * 2'),
            Raw::expression("'Yuriy' as author"),
        ]);
    }
}