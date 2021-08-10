<?php

namespace test\suql\models;

use test\suql\records\ActiveRecord;

class ViewModel extends ActiveRecord
{
    public function query()
    {
        return 'view_model';
    }

    public function table()
    {
        return null;
    }

    public function fields()
    {
        return [];
    }

    public function real()
    {
        return true;
    }

    public function view()
    {
        return <<<SQL
            select
                u.*,
                g.name
            from users u
            join user_group ug on u.id = ug.user_id
            join groups g on g.id = ug.group_id
SQL;
    }
}
