<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class Query2 extends ActiveRecord
{
    public function table()
    {
        return ['table_2' => 't2'];
    }

    public function fields()
    {
        return [
            'f1',
            'f2',
            'f3',
        ];
    }
}