<?php

namespace resurs\models;

use resurs\records\ResursRecord;
use suql\syntax\Field;

class VtView extends ResursRecord
{
    public function query()
    {
        return 'vt_view';
    }

    public function table()
    {
        return 'c2000vt';
    }

    public function view()
    {
        return
            $this
                ->select([
                    'Obj_Id_Device' => 'id',
                    'Type',
                    'Obj_Id_User' => 'user_id',
                ])
                ->get('c2000vt_values')
                    ->select([
                        new Field(['UpdateTime' => 'Time'], [
                            'date_format' => ['%d %M %H:%i']
                        ]),
                        new Field(['UpdateTime' => 'fTime'], [
                            'date_format' => ['%d.%m.%Y']
                        ]),
                        'UpdateTime',
                        'Value',
                    ])
                ->order([
                    'Obj_Id_Device' => 'asc',
                ]);
    }
}
