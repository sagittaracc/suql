<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\syntax\parser\Yaml;
use suql\syntax\SuQL1;
use test\suql\models\Query1;

final class YamlSuQLTest extends TestCase
{
    public function testYamlSuQLParse(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q34.php'));
        $actual = SuQL1::query('tests/suql/yaml/Query1.yaml', new Yaml)->getRawSql();
        $this->assertEquals($expected, $actual);
    }

    public function testYamlSuQLAfterParsing(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q35.php'));
        $actual = SuQL1::query('tests/suql/yaml/Query2.yaml', new Yaml)->order(['f2' => 'asc'])->getRawSql();
        $this->assertEquals($expected, $actual);
    }

    public function testYamlSuQLJoin(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q36.php'));
        $actual = SuQL1::query('tests/suql/yaml/Query3.yaml', new Yaml)->getRawSql();
        $this->assertEquals($expected, $actual);
    }

    public function testJoinWithYaml(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q39.php'));
        $actual1 = Query1::all()->join(SuQL1::query('tests/suql/yaml/Query5.yaml', new Yaml))->getRawSql();
        $this->assertEquals($expected, $actual1);
    }

    public function testJoinYamlWithYaml(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q40.php'));
        $actual1 = SuQL1::query('tests/suql/yaml/Query6.yaml', new Yaml)->join(SuQL1::query('tests/suql/yaml/Query5.yaml', new Yaml))->getRawSql();
        $this->assertEquals($expected, $actual1);
    }
}
