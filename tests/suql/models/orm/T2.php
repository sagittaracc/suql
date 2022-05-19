<?php

namespace test\suql\models;

use suql\db\Container;
use suql\syntax\entity\SuQLTable;

# [Table(name="ot2")]
class T2 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public $b1;
    public $b2;

    public function getDb()
    {
        return Container::get('db_test');
    }
}
