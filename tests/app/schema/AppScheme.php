<?php

namespace app\schema;

use suql\core\Scheme;

class AppScheme extends Scheme
{
    function __construct()
    {
        $this->rel('users', 'user_group', 'users.id = user_group.user_id');
        $this->rel('user_group', 'groups', 'user_group.group_id = groups.id');

        $this->rel('users', 'last_registration', 'users.registration = last_registration.lastRegistration');
    }
}