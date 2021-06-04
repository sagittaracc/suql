<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class FirstGroup extends ActiveRecord
{
    public function query()
    {
        return 'first_group';
    }

    public function table()
    {
        return 'groups';
    }

    public function fields()
    {
        return [];
    }

    public function view()
    {
        return
            $this
                ->select(['*'])
                ->limit(1);
    }
}