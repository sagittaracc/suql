<?php

namespace test\suql\models\relational;

use suql\annotation\attributes\Table;
use suql\builder\MySQLBuilder;
use suql\syntax\entity\SuQLTable;
use test\suql\schema\AppScheme;

#[Table(name: "categories")]
class Categories extends SuQLTable
{
    protected static $schemeClass = AppScheme::class;
    protected static $builderClass = MySQLBuilder::class;
}