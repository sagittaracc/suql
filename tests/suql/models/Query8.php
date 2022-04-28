<?php

namespace test\suql\models;

use suql\syntax\entity\SuQLTable;

class Query8 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function table()
    {
        return null;
    }

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