<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\syntax\parser\Tsml;
use suql\syntax\SuQL1;

final class TsmlSuQLTest extends TestCase
{
    public function testTsmlSuQLParse(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q34.php'));
        $actual = SuQL1::query('tests/suql/tsml/Query1.tsml', Tsml::class)->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}
