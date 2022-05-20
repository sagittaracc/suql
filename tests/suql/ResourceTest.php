<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\syntax\Yaml;

final class ResourceTest extends TestCase
{
    public function testResource(): void
    {
        $expected = StringHelper::trimSql(require('queries/resource/q1.php'));
        $actual = Yaml::query('tests/suql/yaml/resource/LastConsumptionTime.yaml')->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}
