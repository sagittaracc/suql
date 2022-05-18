<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\syntax\YamlSuQL;

final class YamlSuQLTest extends TestCase
{
    public function testYamlSuQLParse(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q34.php'));
        $actual = YamlSuQL::parse('tests/suql/yaml/Query1.yaml')->getRawSql();
        $this->assertEquals($expected, $actual);
    }

    public function testYamlSuQLAfterParsing(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q35.php'));
        $actual = YamlSuQL::parse('tests/suql/yaml/Query2.yaml')->order(['f2' => 'asc'])->getRawSql();
        $this->assertEquals($expected, $actual);
    }

    public function testYamlSuQLJoin(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q36.php'));
        $actual = YamlSuQL::parse('tests/suql/yaml/Query3.yaml')->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}
