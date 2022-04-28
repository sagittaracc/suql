<?php

namespace test\suql\models;

use suql\db\Container;
use suql\syntax\entity\SuQLTable;

class T3 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public $c1;
    public $c2;

    public function table()
    {
        return 'ot3';
    }

    public function getDb()
    {
        return Container::get('db_test');
    }
}
