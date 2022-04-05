<?php

namespace test\suql\models;

use suql\db\Container;
use tests\suql\entity\SuQLTable;

class T2 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public $b1;
    public $b2;

    public function table()
    {
        return 'ot2';
    }

    public function getDb()
    {
        return Container::get('db_test');
    }
}
