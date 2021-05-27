<?php

namespace resurs\models;

use resurs\records\ResursRecord;

class TariffUsedByUsers extends ResursRecord
{
    public function query()
    {
        return 'tariff_used_by_users';
    }

    public function table()
    {
        return 'consumption';
    }

    public function view()
    {
        return
            $this
                ->distinct()
                ->select([
                    'AI_Tarif' => 'tarif_id',
                ])
                ->join('counter')
                    ->select([
                        'Obj_Id_Counter' => 'counter_id',
                        'Obj_Id_User' => 'user_id',
                    ]);
    }
}
