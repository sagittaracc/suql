<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\syntax\SuQL1;
use test\suql\models\Query1;

final class TsmlSuQLTest extends TestCase
{
    public function testTsmlSuQLParse(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q34.php'));
        $actual = SuQL1::query('tests/suql/tsml/Query1.tsml')->getRawSql();
        $this->assertEquals($expected, $actual);
    }

    public function testTsmlSuQLAfterParsing(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q35.php'));
        $actual = SuQL1::query('tests/suql/tsml/Query2.tsml')->order(['f2' => 'asc'])->getRawSql();
        $this->assertEquals($expected, $actual);
    }

    public function testTsmlSuQLJoin(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q36.php'));
        $actual = SuQL1::query('tests/suql/tsml/Query3.tsml')->getRawSql();
        $this->assertEquals($expected, $actual);
    }

    public function testJoinWithTsml(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q39.php'));
        $actual1 = Query1::all()->join(SuQL1::query('tests/suql/tsml/Query5.tsml'))->getRawSql();
        $this->assertEquals($expected, $actual1);
    }

    public function testJoinTsmlWithTsml(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q40.php'));
        $actual1 = SuQL1::query('tests/suql/tsml/Query6.tsml')->join(SuQL1::query('tests/suql/tsml/Query5.tsml'))->getRawSql();
        $this->assertEquals($expected, $actual1);
    }
}
