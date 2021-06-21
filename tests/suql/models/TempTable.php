<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class TempTable extends ActiveRecord
{
    public function table()
    {
        return 'temp_table';
    }

    public function create()
    {
        return [
            'id' => 'integer',
            'name' => 'string',
        ];
    }

    public function fields()
    {
        return [];
    }
}
