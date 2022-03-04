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
            'temp_table' => 'tt',
            'table_name' => 'tn',
            // ---
            'table_1' => 't1',
            'table_2' => 't2',
            'table_3' => 't3',
            'table_9' => 't9',
            'table_10' => 't10',
            'table_15' => 't15',
        ]);

        // Связь между реальными таблицами в базе данных
        $this->rel('{{u}}', '{{ug}}', '{{u}}.id = {{ug}}.user_id');
        $this->rel('{{ug}}', '{{g}}', '{{ug}}.group_id = {{g}}.id');
        // Связь с временными таблицами
        $this->rel('{{tt}}', '{{tn}}', '{{tt}}.id = {{tn}}.field');

        // Связи с абстрактными вьюхами
        $this->rel('users', 'last_registration', 'users.registration = last_registration.lastRegistration');
        $this->rel('user_group', 'first_group', 'user_group.group_id = first_group.id');

        $this->rel('{{t1}}', '{{t2}}', '{{t1}}.id = {{t2}}.id');
        $this->rel('{{t2}}', '{{t3}}', '{{t2}}.id = {{t3}}.id');
        $this->rel('table_2', 'query_15', 'table_2.id = table_15.id');
        $this->rel('{{t9}}', '{{t10}}', '{{t9}}.p1 = {{t10}}.f1');

        $this->rel('table_1', 'query_13', 'table_1.id = table_13.id');
    }
}