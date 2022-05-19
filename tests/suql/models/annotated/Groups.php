<?php

namespace test\suql\models;

use suql\syntax\entity\SuQLTable;

# [Table(name="groups")]
class Groups extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';
}