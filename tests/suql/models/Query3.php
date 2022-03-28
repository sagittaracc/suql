<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class Query3 extends ActiveRecord
{
    public function table()
    {
        return 'table_3';
    }
}