<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\models\Query1;
use test\suql\models\QuerySome;

final class SelectTest extends TestCase
{
    public function testSelectAll(): void
    {
        $actual = Query1::all()->getRawSql();

        $expected = StringHelper::trimSql(<<<SQL
            select
                *
            from table_1
SQL);

        $this->assertEquals($expected, $actual);
    }

    public function testSelectAllWithTableName(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                table_1.*
            from table_1
SQL);

        $query = Query1::all()->select(['*']);

        $this->assertEquals($sql, $query->getRawSql());
    }

    public function testSelectFieldList(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                table_1.f1,
                table_1.f2
            from table_1
SQL);

        $query = Query1::all()->select(['f1', 'f2']);

        $this->assertEquals($sql, $query->getRawSql());
    }

    public function testSelectUsingAliases(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                table_1.f1 as af1,
                table_1.f2 as af2
            from table_1
SQL);

        $query = Query1::all()->select([
            'f1' => 'af1',
            'f2' => 'af2',
        ]);

        $this->assertEquals($sql, $query->getRawSql());
    }

    public function testSelectWithTableAlias(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                t2.f1,
                t2.f2,
                t2.f3
            from table_2 t2
SQL);

        $query = QuerySome::all();

        $this->assertEquals($sql, $query->getRawSql());
    }
}
