<?php

namespace resurs\models;

use resurs\records\ResursRecord;
use suql\syntax\Field;

class VtDateOfLastData extends ResursRecord
{
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
                new Field(['UpdateTime' => 'lastUpdateTime'], [
                    'max'
                ]),
            ])
            ->group('Obj_Id_Device');
    }
}
