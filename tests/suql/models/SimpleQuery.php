<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class SimpleQuery extends ActiveRecord
{
    public function table()
    {
        return 'table_1';
    }

    public function fields()
    {
        return [];
    }
}
