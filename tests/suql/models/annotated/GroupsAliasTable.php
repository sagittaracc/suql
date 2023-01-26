<?php

namespace test\suql\models;

use suql\annotation\attributes\Table;
use suql\builder\MySQLBuilder;
use suql\syntax\entity\SuQLTable;
use test\suql\schema\AppScheme;

#[Table(name: "groups", alias: "g")]
class GroupsAliasTable extends SuQLTable
{
    protected static $schemeClass = AppScheme::class;
    protected static $builderClass = MySQLBuilder::class;
}