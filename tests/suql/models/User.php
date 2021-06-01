<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class User extends ActiveRecord
{
    public function table()
    {
        return 'users';
    }

    public function fields()
    {
        return [
            'UpdateTime',
            'ConsumptionDelta',
            'MoneyNotPaidDelta',
        ];
    }
}