<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use resurs\models\ArchiveView;
use resurs\models\Auth;
use resurs\models\TariffUsedByUsers;
use resurs\models\VtDateOfLastData;
use resurs\models\VtLastData;
use resurs\models\VtValues;
use resurs\models\VtView;
use sagittaracc\StringHelper;
use suql\core\SimpleParam;
use suql\syntax\Expression;

class ResursTest extends TestCase
{
    public function testQueries(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
                select
                    c2000vt_values.AI_c2000vt_values as id,
                    c2000vt_values.Obj_Id_Device as device_id,
                    max(c2000vt_values.UpdateTime) as lastUpdateTime
                from c2000vt_values
                group by c2000vt_values.Obj_Id_Device
SQL);

        $query = VtDateOfLastData::all();

        $this->assertEquals($sql, $query->getRawSql());

        $sql = StringHelper::trimSql(<<<SQL
            select
                *
            from c2000vt_values
SQL);

        $query = VtValues::all();

        $this->assertEquals($sql, $query->getRawSql());

        $sql = StringHelper::trimSql(<<<SQL
            select
                c2000vt_values.Obj_Id_Device as device_id,
                c2000vt_values.Value as value,
                c2000vt_values.UpdateTime as time,
                vt_date_of_last_data.id,
                c2000vt.Type as type,
                c2000vt.Obj_Id_User as user_id,
                c2000vt.Obj_Id_Home as home_id,
                c2000vt.Name as name
            from c2000vt_values
            inner join
            (
                select
                    c2000vt_values.AI_c2000vt_values as id,
                    c2000vt_values.Obj_Id_Device as device_id,
                    max(c2000vt_values.UpdateTime) as lastUpdateTime
                from c2000vt_values
                group by c2000vt_values.Obj_Id_Device
            ) vt_date_of_last_data
                on c2000vt_values.Obj_Id_Device = vt_date_of_last_data.device_id
               and c2000vt_values.UpdateTime = vt_date_of_last_data.lastUpdateTime
            inner join c2000vt on c2000vt.Obj_Id_Device = c2000vt_values.Obj_Id_Device
SQL);

        $query = VtLastData::all();

        $this->assertEquals($sql, $query->getRawSql());

        $sql = StringHelper::trimSql(<<<SQL
            select
                c2000vt.Obj_Id_Device as id,
                c2000vt.Type,
                c2000vt.Obj_Id_User as user_id,
                case
                    when c2000vt.Type = 'Temperature' then 'C'
                    when c2000vt.Type = 'Humidity' then '%'
                end as Unit,
                date_format(c2000vt_values.UpdateTime, '%d %M %H:%i') as Time,
                date_format(c2000vt_values.UpdateTime, '%d.%m.%Y') as fTime,
                c2000vt_values.UpdateTime,
                c2000vt_values.Value
            from c2000vt
            inner join c2000vt_values on c2000vt.Obj_Id_Device = c2000vt_values.Obj_Id_Device
            order by c2000vt_values.Obj_Id_Device asc
SQL);

        $query = VtView::all();

        $this->assertEquals($sql, $query->getRawSql());

        $sql = StringHelper::trimSql(<<<SQL
            select distinct
                consumption.AI_Tarif as tarif_id,
                counter.Obj_Id_Counter as counter_id,
                counter.Obj_Id_User as user_id
            from consumption
            inner join counter on consumption.Obj_Id_Counter = counter.Obj_Id_Counter
SQL);

        $query = TariffUsedByUsers::all();

        $this->assertEquals($sql, $query->getRawSql());

        $sql = StringHelper::trimSql(<<<SQL
            select
                consumption.UpdateTime,
                consumption.ConsumptionDelta,
                consumption.MoneyNotPaidDelta,
                date_format(consumption.UpdateTime, '%d %M %Y') as date,
                counter.Obj_Id_Counter as id,
                counter.Obj_Id_User,
                CONCAT(counter.Name, IF(counter.SerialNumber <> '' AND counter.SerialNumber IS NOT NULL, CONCAT(counter.SerialNumber, ' (', consumption.NumberTarif + 1,')'), '')) AS SerialNumber,
                resurs.Name_Resurs,
                resurs.Unit as units,
                resurs.id_Resurs,
                users.FIO,
                users.Address,
                CONCAT(tarif.Name, ' (', FORMAT(tarif.Price, 0), ' р.)') AS tarif
            from consumption
            inner join counter on consumption.Obj_Id_Counter = counter.Obj_Id_Counter
            inner join resurs on counter.Id_Resurs = resurs.id_Resurs
            inner join users on counter.Obj_Id_User = users.Obj_Id_User
            left join tarif on consumption.AI_Tarif = tarif.AI_Tarif
            order by consumption.UpdateTime desc, consumption.Obj_Id_Counter asc
SQL);

        $query = ArchiveView::all();

        $this->assertEquals($sql, $query->getRawSql());
    }
}