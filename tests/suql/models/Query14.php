<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class Query14 extends ActiveRecord
{
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
