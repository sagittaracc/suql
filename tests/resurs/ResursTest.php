<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use resurs\models\VtDateOfLastData;
use resurs\models\VtLastData;
use resurs\models\VtValues;
use resurs\models\VtView;
use sagittaracc\StringHelper;

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
    }
}