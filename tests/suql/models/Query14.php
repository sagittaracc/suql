<?php

namespace test\suql\models;

use tests\suql\entity\SuQLTable;

class Query14 extends SuQLTable
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';

    public function table()
    {
        $query1 = Query1::all()
            ->select(['f1', 'f2', 'f3'])
            ->as('query1');

        $query2 = Query2::all()
            ->select(['f1', 'f2', 'f3'])
            ->as('query2');

        return $query1->and([$query2])->as('query_14');
    }
}
