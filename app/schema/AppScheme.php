<?php

namespace app\schema;

use suql\core\Scheme;

class AppScheme extends Scheme
{
    function __construct()
    {
        // $this->rel('users', 'user_group', 'users.id = user_group.user_id');
    }
}