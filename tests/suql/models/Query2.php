<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class Query2 extends ActiveRecord
{
    public function table()
    {
        return 'table_2';
    }

    public function fields()
    {
        return [];
    }
}