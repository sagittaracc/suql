<?php

namespace test\suql\models;

use suql\syntax\entity\SuQLTable;

# [Table(name="table_1")]
class Query21 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\UndefinedSchema';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';
}