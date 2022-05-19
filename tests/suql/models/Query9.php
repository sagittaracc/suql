<?php

namespace test\suql\models;

use suql\db\Container;
use suql\syntax\entity\SuQLTable;

# [Table(name="table_9")]
class Query9 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function create()
    {
        return $this
            ->column('p1')
                ->setType('int')
                ->setLength(11)
                ->setDefault(0)
            ->column('p2')
                ->setType('varchar')
                ->setLength(255)
                ->setDefault('');
    }

    public function getDb()
    {
        return Container::get('db_test');
    }
}
