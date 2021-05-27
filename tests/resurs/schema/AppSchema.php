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
    }
}