<?php

namespace test\suql\models;

use suql\syntax\entity\SuQLTable;

class GroupsNullTable extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';
}