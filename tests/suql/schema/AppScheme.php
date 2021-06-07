<?php

namespace test\suql\schema;

use suql\core\Scheme;

class AppScheme extends Scheme
{
    function __construct()
    {
        $this->addTableList([
            'users' => 'u',
            'user_group' => 'ug',
            'groups' => 'g',
        ]);

        // Связь между реальными таблицами в базе данных
        $this->rel('{{u}}', '{{ug}}', '{{u}}.id = {{ug}}.user_id');
        $this->rel('{{ug}}', '{{g}}', '{{ug}}.group_id = {{g}}.id');
        $this->rel('users', 'user_group', 'users.id = user_group.user_id');
        $this->rel('user_group', 'groups', 'user_group.group_id = groups.id');

        // Связи с абстрактными вьюхами
        $this->rel('users', 'last_registration', 'users.registration = last_registration.lastRegistration');
        $this->rel('user_group', 'first_group', 'user_group.group_id = first_group.id');
    }
}