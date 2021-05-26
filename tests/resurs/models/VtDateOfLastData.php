<?php

namespace resurs\models;

use resurs\fields\Max;
use suql\syntax\SuQL;

class VtDateOfLastData extends SuQL
{
    protected static $schemeClass = 'resurs\\schema\\AppSchema';
    protected static $sqlDriver = 'mysql';

    public function query()
    {
        return 'vt_date_of_last_data';
    }

    public function table()
    {
        return 'c2000vt_values';
    }

    public function view()
    {
        return
            $this->select([
                'AI_c2000vt_values' => 'id',
                'Obj_Id_Device' => 'device_id',
                new Max(['UpdateTime' => 'lastUpdateTime']),
            ])
            ->group('Obj_Id_Device');
    }
}