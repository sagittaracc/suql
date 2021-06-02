<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class NoName extends ActiveRecord
{
    public function query()
    {
        return 'no_name';
    }

    public function table()
    {
        return ActiveGroups::all();
    }

    public function fields()
    {
        return [];
    }

    public function view()
    {
        return $this->select([
            'name',
            'count',
        ]);
    }
}