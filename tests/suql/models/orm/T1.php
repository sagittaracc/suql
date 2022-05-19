<?php

namespace test\suql\models;

use suql\db\Container;
use suql\syntax\entity\SuQLTable;

# [Table(name="ot1")]
class T1 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public $a1;
    public $a2;

    public function getDb()
    {
        return Container::get('db_test');
    }
}
