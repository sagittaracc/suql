<?php

namespace test\suql\models;

use suql\builder\MySQLBuilder;
use suql\syntax\entity\SuQLTable;
use test\suql\schema\AppScheme;

class Query8 extends SuQLTable
{
    protected static $schemeClass = AppScheme::class;
    protected static $builderClass = MySQLBuilder::class;

    public function view()
    {
        return
            $this->union([
                Query1::all()->select(['f1', 'f2', 'f3'])->as('query1'),
                Query2::all()->select(['f1', 'f2', 'f3'])->as('query2'),
                Query3::all()->select(['f1', 'f2', 'f3'])->as('query3'),
            ]);
    }
}