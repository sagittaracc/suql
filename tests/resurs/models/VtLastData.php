<?php

namespace resurs\models;

use resurs\records\ResursRecord;

class VtLastData extends ResursRecord
{
    public function query()
    {
        return 'vt_last_data';
    }

    public function table()
    {
        return 'c2000vt_values';
    }

    public function view()
    {
        return
            $this
                ->select([
                    'Obj_Id_Device' => 'device_id',
                    'Value' => 'value',
                    'UpdateTime' => 'time',
                ])
                ->join(VtDateOfLastData::all())
                    ->select([
                        'id',
                    ])
                ->join('c2000vt')
                    ->select([
                        'Type' => 'type',
                        'Obj_Id_User' => 'user_id',
                        'Obj_Id_Home' => 'home_id',
                        'Name' => 'name',
                    ]);
    }
}
