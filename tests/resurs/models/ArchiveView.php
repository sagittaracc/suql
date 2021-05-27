<?php

namespace resurs\models;

use resurs\records\ResursRecord;
use suql\syntax\Field;
use suql\syntax\Raw;

class ArchiveView extends ResursRecord
{
    public function query()
    {
        return 'archive_view';
    }

    public function table()
    {
        return 'consumption';
    }

    public function view()
    {
        return
            $this
                ->select([
                    'UpdateTime',
                    'ConsumptionDelta',
                    'MoneyNotPaidDelta',
                    new Field(['UpdateTime' => 'date'], [
                        'date_format' => ['%d %M %Y'],
                    ]),
                ])
                ->order([
                    'UpdateTime' => 'desc',
                    'Obj_Id_Counter',
                ])
                ->join('counter')
                    ->select([
                        'Obj_Id_Counter' => 'id',
                        'Obj_Id_User',
                        Raw::expression("CONCAT(counter.Name, IF(counter.SerialNumber <> '' AND counter.SerialNumber IS NOT NULL, CONCAT(counter.SerialNumber, ' (', consumption.NumberTarif + 1,')'), '')) AS SerialNumber"),
                    ])
                ->join('resurs')
                    ->select([
                        'Name_Resurs',
                        'Unit' => 'units',
                        'id_Resurs',
                    ])
                ->join('users')
                    ->select([
                        'FIO',
                        'Address',
                    ])
                ->leftJoin('tarif')
                    ->select([
                        Raw::expression("CONCAT(tarif.Name, ' (', FORMAT(tarif.Price, 0), ' р.)') AS tarif")
                    ]);
    }
}
