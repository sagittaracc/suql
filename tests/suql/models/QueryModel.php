<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class QueryModel extends ActiveRecord
{
    public function query()
    {
        return 'query_model';
    }

    public function table()
    {
        return NestedQueryModel::all()->select(['field_1', 'field_2', 'field_3']);
    }

    public function fields()
    {
        return [];
    }

    public function view()
    {
        return $this->select([
            'field_1',
            'field_2',
        ]);
    }
}