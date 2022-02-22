<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class NestedQueryModel extends ActiveRecord
{
    public function query()
    {
        return 'nested_query_model';
    }

    public function table()
    {
        return TableModel::all()->select(['field_1', 'field_2', 'field_3']);
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