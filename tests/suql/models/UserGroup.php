<?php

namespace test\suql\models;

use suql\syntax\Field;
use suql\syntax\Raw;
use test\suql\records\ActiveRecord;

class UserGroup extends ActiveRecord
{
    public function table()
    {
        return 'user_group';
    }

    public function fields()
    {
        return [
            'group_id',
            new Field(['field' => 'alias'], [
                'func' => [3],
            ]),
            Raw::expression('CONCAT(shit)'),
        ];
    }
}