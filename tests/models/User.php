<?php

namespace app\models;

use suql\syntax\SuQL;

class User extends SuQL
{
    public function query()
    {
        return 'all_user_list';
    }

    public function table()
    {
        return 'users';
    }
}