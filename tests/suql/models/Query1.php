<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class Query1 extends ActiveRecord
{
    public function table()
    {
        return ['table' => 't'];
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