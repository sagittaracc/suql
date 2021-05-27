<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use resurs\models\VtDateOfLastData;
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
    }
}