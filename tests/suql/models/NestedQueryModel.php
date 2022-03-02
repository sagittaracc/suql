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
        return 'table_1';
    }

    public function fields()
    {
        return [];
    }
}
