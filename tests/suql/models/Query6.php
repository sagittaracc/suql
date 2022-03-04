<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class Query6 extends ActiveRecord
{
    public function table()
    {
        return Query7::all()->select(['field_1', 'field_2', 'field_3']);
    }

    public function fields()
    {
        return [
            'field_1',
            'field_2',
        ];
    }
}