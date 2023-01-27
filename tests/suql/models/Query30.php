<?php

namespace test\suql\models;

use suql\annotation\attributes\Table;
use suql\builder\MySQLBuilder;
use suql\syntax\entity\SuQLTable;
use test\suql\schema\AppScheme;

#[Table(name: "table_30")]
class Query30 extends SuQLTable
{
    protected static $schemeClass = AppScheme::class;
    protected static $builderClass = MySQLBuilder::class;

    protected $useMacros = true;
    protected $macrosPath = 'tests/suql/macros';
}