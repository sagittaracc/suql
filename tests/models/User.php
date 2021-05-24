<?php

namespace app\models;

use suql\syntax\SuQL;

class User extends SuQL
{
    public function query()
    {
        return 'app_models_User';
    }

    public function table()
    {
        return 'users';
    }
}