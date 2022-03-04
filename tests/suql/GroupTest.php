<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\models\Query1;

final class GroupTest extends TestCase
{
    public function testSelectGroup(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                table_3.f1,
                count(table_3.f1) as count
            from table_1
            inner join table_2 on table_1.id = table_2.id
            inner join table_3 on table_2.id = table_3.id
            group by table_3.f1
SQL);

        $query = Query1::all()
            ->getQuery2()
            ->getQuery3()
                ->select(['f1'])
            ->group('f1')
            ->count(['f1' => 'count']);
        
        $this->assertEquals($sql, $query->getRawSql());
    }
}