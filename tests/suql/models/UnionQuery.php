<?php

namespace test\suql\models;

use suql\syntax\field\Field;
use test\suql\records\ActiveRecord;

class UnionQuery extends ActiveRecord
{
    public function query()
    {
        return 'union_query';
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
        return
            $this->union([
                User::all()
                    ->select([
                        new Field(['registration' => 'reg_interval'], [
                            'min'
                        ])
                    ])
                    ->as('q1'),
                User::all()
                    ->select([
                        new Field(['registration' => 'reg_interval'], [
                            'max'
                        ])
                    ])
                    ->as('q2'),
            ]);
    }
}