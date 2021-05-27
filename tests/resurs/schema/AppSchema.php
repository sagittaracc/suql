<?php

namespace resurs\schema;

use suql\core\Scheme;

class AppSchema extends Scheme
{
    function __construct()
    {
        $this->rel(
            ['c2000vt_values' => 't1'],
            ['vt_date_of_last_data' => 't2'],
            't1.Obj_Id_Device = t2.device_id and t1.UpdateTime = t2.lastUpdateTime'
        );

        $this->rel(
            ['c2000vt' => 't1'],
            ['c2000vt_values' => 't2'],
            't1.Obj_Id_Device = t2.Obj_Id_Device'
        );

        $this->rel(
            ['consumption' => 't1'],
            ['counter' => 't2'],
            't1.Obj_Id_Counter = t2.Obj_Id_Counter'
        );

        $this->rel(
            ['counter' => 't1'],
            ['resurs' => 't2'],
            't1.Id_Resurs = t2.id_Resurs'
        );

        $this->rel(
            ['counter' => 't1'],
            ['users' => 't2'],
            't1.Obj_Id_User = t2.Obj_Id_User'
        );

        $this->rel(
            ['consumption' => 't1'],
            ['tarif' => 't2'],
            't1.AI_Tarif = t2.AI_Tarif'
        );
    }
}