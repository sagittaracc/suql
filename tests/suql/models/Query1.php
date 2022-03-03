<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class Query1 extends ActiveRecord
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