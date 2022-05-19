<?php

namespace test\suql\models;

use suql\syntax\entity\SuQLTable;

# [Table(name="table_2")]
class Query2 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';
}