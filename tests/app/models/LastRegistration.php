<?php

namespace app\models;

use app\records\ActiveRecord;
use suql\syntax\Field;

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

    public function view()
    {
        return $this->select([
            new Field(['registration' => 'lastRegistration'], [
                'max',
            ])
        ]);
    }
}