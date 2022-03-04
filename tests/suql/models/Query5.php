<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class Query5 extends ActiveRecord
{
    public function table()
    {
        return null;
    }

    public function fields()
    {
        return [];
    }

    public function view()
    {
        return $this->select([
            '2 * 2',
            "'Yuriy' as author",
        ]);
    }
}