<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\models\QueryModel;
use test\suql\models\SubUnion;

final class NestedQueryTest extends TestCase
{
    public function testNestedQuery(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                nested_query_model.field_1,
                nested_query_model.field_2
            from (
                select
                    table_1.field_1,
                    table_1.field_2,
                    table_1.field_3
                from table_1
            ) nested_query_model
SQL);

        $query = QueryModel::all();

        $this->assertEquals($sql, $query->getRawSql());
    }

    public function testSubUnion(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select * from (
                (select min(users.registration) as reg_interval from users)
                union
                (select max(users.registration) as reg_interval from users)
            ) last_registration
SQL);
        $query = SubUnion::all();

        $this->assertEquals($sql, $query->getRawSql());
    }
}