<?php

namespace app\models;

use suql\syntax\Modifier;
use suql\syntax\SuQL;

class LastRegistration extends SuQL
{
    protected static $schemeClass = 'app\\schema\\AppScheme';
    protected static $sqlDriver = 'mysql';

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
            new \suql\syntax\Field(['registration' => 'lastRegistration'], [
                'max',
            ])
        ]);
    }
}