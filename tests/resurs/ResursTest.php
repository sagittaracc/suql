<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use resurs\models\VtDateOfLastData;
use resurs\models\VtLastData;
use resurs\models\VtValues;
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
                vt_date_of_last_data.id as id,
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
                on c2000vt_values.Obj_Id_Device = vt_date_of_last_data.Obj_Id_Device
               and c2000vt_values.UpdateTime = vt_date_of_last_data.UpdateTime
            inner join c2000vt on c2000vt_values.Obj_Id_Device = c2000vt.Obj_Id_Device
SQL);

        $query = VtLastData::all();

        $this->assertEquals($sql, $query->getRawSql());
    }
}