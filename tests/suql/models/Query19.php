<?php

namespace test\suql\models;

use test\suql\models\tables\TestMySQLTable;

# [Table(name="table_19")]
class Query19 extends TestMySQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public $c1;
    public $c2;

    public function create()
    {
        return
            $this->column('c1')
                    ->setType('int')
                    ->setLength(11)
                    ->setDefault(0)
                ->column('c2')
                    ->setType('varchar')
                    ->setLength(255)
                    ->setDefault(null);
                
    }
}
