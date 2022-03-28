<?php

namespace test\suql\models;

use suql\syntax\field\Raw;
use test\suql\records\ActiveRecord;

class Query5 extends ActiveRecord
{
    public function table()
    {
        return null;
    }

    public function view()
    {
        return $this->select([
            Raw::expression('2 * 2'),
            Raw::expression("'Yuriy' as author"),
        ]);
    }
}