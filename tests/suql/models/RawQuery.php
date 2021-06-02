<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class RawQuery extends ActiveRecord
{
    public function query()
    {
        return 'raw_query';
    }

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