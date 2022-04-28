<?php

namespace test\suql\models;

use suql\db\Container;
use suql\syntax\entity\SuQLTable;

class Query11 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public $f1;
    public $f2;

    public function table()
    {
        return 'table_10';
    }

    public function create()
    {
        return
            $this
                ->column('f1')
                    ->setType('int')
                    ->setLength(11)
                    ->autoIncrement()
                    ->primaryKey()
                ->column('f2')
                    ->setType('int')
                    ->setLength(11);
    }

    public function getDb()
    {
        return Container::get('db_test');
    }
}
