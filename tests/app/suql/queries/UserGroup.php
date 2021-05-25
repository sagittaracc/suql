<?php

namespace app\models;

use suql\syntax\SuQL;

class UserGroup extends SuQL
{
    protected static $schemeClass = 'app\\schema\\AppScheme';
    protected static $sqlDriver = 'mysql';

    public function query()
    {
        return 'app_models_UserGroup';
    }

    public function table()
    {
        return 'user_group';
    }

    public function view()
    {
        return $this;
    }
}