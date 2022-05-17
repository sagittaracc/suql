<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\syntax\JsonSuQL;

final class JsonSuQLTest extends TestCase
{
    public function testJsonSuQLParse(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q4.php'));
        $actual = JsonSuQL::parse('tests/suql/json/Query1.json')->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}
