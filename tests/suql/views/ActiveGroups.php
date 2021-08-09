<?php

namespace test\suql\views;

class ActiveGroups
{
    public function query()
    {
        return <<<SQL
            select
                *
            from users u
            join user_group ug on u.id = ug.user_id
            join groups g on ug.group_id = g.id
SQL;
    }
}