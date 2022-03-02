<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class Query extends ActiveRecord
{
    public function table()
    {
        return NestedQuery::all()->select(['field_1', 'field_2', 'field_3']);
    }

    public function fields()
    {
        return [
            'field_1',
            'field_2',
        ];
    }
}