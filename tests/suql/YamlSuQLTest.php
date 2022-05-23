<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\syntax\Yaml;
use test\suql\models\Query1;

final class YamlSuQLTest extends TestCase
{
    public function testYamlSuQLParse(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q34.php'));
        $actual = Yaml::query('tests/suql/yaml/Query1.yaml')->getRawSql();
        $this->assertEquals($expected, $actual);
    }

    public function testYamlSuQLAfterParsing(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q35.php'));
        $actual = Yaml::query('tests/suql/yaml/Query2.yaml')->order(['f2' => 'asc'])->getRawSql();
        $this->assertEquals($expected, $actual);
    }

    public function testYamlSuQLJoin(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q36.php'));
        $actual = Yaml::query('tests/suql/yaml/Query3.yaml')->getRawSql();
        $this->assertEquals($expected, $actual);
    }

    public function testJoinWithYaml(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q39.php'));
        $actual1 = Query1::all()->join(Yaml::query('tests/suql/yaml/Query5.yaml'))->getRawSql();
        $this->assertEquals($expected, $actual1);
    }

    public function testJoinYamlWithYaml(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q40.php'));
        $actual1 = Yaml::query('tests/suql/yaml/Query6.yaml')->join(Yaml::query('tests/suql/yaml/Query5.yaml'))->getRawSql();
        $this->assertEquals($expected, $actual1);
    }
}
