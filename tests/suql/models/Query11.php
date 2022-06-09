<?php

namespace test\suql\models;

use test\suql\models\tables\TestMySQLTable;
use test\suql\schema\AppScheme;

# [Table(name="table_10")]
class Query11 extends TestMySQLTable
{
    protected static $schemeClass = AppScheme::class;

    public $f1;
    public $f2;

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
}
