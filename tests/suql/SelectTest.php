<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\models\Query1;
use test\suql\models\Query2;

final class SelectTest extends TestCase
{
    public function testSelectAll(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                *
            from table_2
SQL);

        $query = Query2::all();

        $this->assertEquals($sql, $query->getRawSql());
    }

    public function testSelectAllWithTableName(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                table_2.*
            from table_2
SQL);

        $query = Query2::all()->select(['*']);

        $this->assertEquals($sql, $query->getRawSql());
    }

    public function testSelectFieldList(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                table_2.f1,
                table_2.f2
            from table_2
SQL);

        $query = Query2::all()->select(['f1', 'f2']);

        $this->assertEquals($sql, $query->getRawSql());
    }

    public function testSelectUsingAliases(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                table_2.f1 as af1,
                table_2.f2 as af2
            from table_2
SQL);

        $query = Query2::all()->select([
            'f1' => 'af1',
            'f2' => 'af2',
        ]);

        $this->assertEquals($sql, $query->getRawSql());
    }

    public function testSelectWithTableAlias(): void
    {
        $sql = StringHelper::trimSql(<<<SQL
            select
                t.f1,
                t.f2,
                t.f3
            from table t
SQL);

        $query = Query1::all();

        $this->assertEquals($sql, $query->getRawSql());
    }
}
