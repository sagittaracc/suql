<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class Query12 extends ActiveRecord
{
    public function table()
    {
        return '{{t1}}';
    }
}