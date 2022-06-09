<?php

namespace test\suql\models;

use suql\builder\MySQLBuilder;
use suql\syntax\entity\SuQLTable;
use test\suql\schema\AppScheme;

class Query14 extends SuQLTable
{
    protected static $schemeClass = AppScheme::class;
    protected static $builderClass = MySQLBuilder::class;

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
