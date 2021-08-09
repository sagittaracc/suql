<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;
use suql\syntax\field\Field;

class LastRegistration extends ActiveRecord
{
    public function query()
    {
        return 'last_registration';
    }

    public function table()
    {
        return 'users';
    }

    public function fields()
    {
        return [];
    }

    public function view()
    {
        return $this->select([
            new Field(['registration' => 'lastRegistration'], [
                'max',
            ])
        ]);
    }
}