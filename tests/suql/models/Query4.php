<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class Query4 extends ActiveRecord
{
    public function table()
    {
        return ['table_4' => 't4'];
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