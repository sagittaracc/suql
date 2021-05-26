<?php

namespace resurs\schema;

use suql\core\Scheme;

class AppSchema extends Scheme
{
    function __construct()
    {
        // $this->rel('users', 'last_registration', 'users.registration = last_registration.lastRegistration');
    }
}