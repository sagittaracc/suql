<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\syntax\YamlSuQL;

final class YamlSuQLTest extends TestCase
{
    public function testJsonSuQLParse(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q34.php'));
        $actual = YamlSuQL::parse('tests/suql/yaml/Query1.yml')->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}
