<?php

namespace test\suql\models;

use suql\syntax\field\Field;
use test\suql\records\ActiveRecord;

class SubUnion extends ActiveRecord
{
    public function query()
    {
        return 'sub_union';
    }

    public function table()
    {
        $query1 = User::all()
            ->select([new Field(['registration' => 'reg_interval'], ['min'])])
            ->as('q1');

        $query2 = User::all()
            ->select([new Field(['registration' => 'reg_interval'], ['max'])])
            ->as('q2');

        return $query1->and([$query2])->as('last_registration');
    }

    public function fields()
    {
        return [];
    }
}
