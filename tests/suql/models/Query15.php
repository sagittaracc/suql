<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class Query15 extends ActiveRecord
{
    public function query()
    {
        return 'query_15';
    }

    public function table()
    {
        return 'table_15';
    }

    public function view()
    {
        return
            $this
                ->select(['*'])
                ->limit(1);
    }
}