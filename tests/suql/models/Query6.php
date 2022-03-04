<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class Query6 extends ActiveRecord
{
    public function table()
    {
        return Query7::all()->select(['f1', 'f2', 'f3']);
    }

    public function fields()
    {
        return [
            'f1',
            'f2',
        ];
    }
}