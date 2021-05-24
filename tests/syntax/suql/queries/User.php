<?php

namespace app\models;

use suql\syntax\SuQL;

class User extends SuQL
{
    protected static $schemeClass = 'app\\schema\\AppScheme';
    protected static $sqlDriver = 'mysql';

    public function query()
    {
        return 'app_models_User';
    }

    public function table()
    {
        return 'users';
    }

    public function view()
    {
        return $this;
    }
}