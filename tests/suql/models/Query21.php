<?php

namespace test\suql\models;

use suql\annotation\attributes\Table;
use suql\builder\MySQLBuilder;
use \test\suql\schema\UndefinedSchema;
use suql\syntax\entity\SuQLTable;

#[Table(name: "table_1")]
class Query21 extends SuQLTable
{
    protected static $schemeClass = UndefinedSchema::class;
    protected static $builderClass = MySQLBuilder::class;
}