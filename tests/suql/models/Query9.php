<?php

namespace test\suql\models;

use test\suql\models\tables\TestMySQLTable;
use test\suql\schema\AppScheme;

# [Table(name="table_9")]
class Query9 extends TestMySQLTable
{
    protected static $schemeClass = AppScheme::class;

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
}
