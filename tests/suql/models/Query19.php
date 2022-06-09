<?php

namespace test\suql\models;

use test\suql\models\tables\TestMySQLTable;
use test\suql\schema\AppScheme;

# [Table(name="table_19")]
class Query19 extends TestMySQLTable
{
    protected static $schemeClass = AppScheme::class;

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
