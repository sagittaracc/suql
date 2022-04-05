<?php

namespace test\suql\models;

use suql\db\Container;
use tests\suql\entity\SuQLTable;

class T1 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public $a1;
    public $a2;

    public function table()
    {
        return 'ot1';
    }

    public function getDb()
    {
        return Container::get('db_test');
    }
}
