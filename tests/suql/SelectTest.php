<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\models\Query1;
use test\suql\models\Query4;

final class SelectTest extends TestCase
{
    public function testSelectAll(): void
    {
        $expected = StringHelper::trimSql(require('queries/q1.php'));
        $actual = Query1::all()->getRawSql();
        $this->assertEquals($expected, $actual);
    }

    public function testSelectAllWithTableName(): void
    {
        $expected = StringHelper::trimSql(require('queries/q2.php'));
        $actual = Query1::all()->select(['*'])->getRawSql();
        $this->assertEquals($expected, $actual);
    }

    public function testSelectFieldList(): void
    {
        $expected = StringHelper::trimSql(require('queries/q3.php'));
        $actual = Query1::all()->select(['f1', 'f2'])->getRawSql();
        $this->assertEquals($expected, $actual);
    }

    public function testSelectUsingAliases(): void
    {
        $expected = StringHelper::trimSql(require('queries/q4.php'));
        $actual = Query1::all()->select([
            'f1' => 'af1',
            'f2' => 'af2',
        ])->getRawSql();
        $this->assertEquals($expected, $actual);
    }

    public function testSelectWithTableAlias(): void
    {
        $expected = StringHelper::trimSql(require('queries/q5.php'));
        $actual = Query4::all()->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}
