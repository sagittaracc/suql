<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\syntax\Yaml;
use test\suql\models\resource\Consumption;

final class ResourceTest extends TestCase
{
    public function testResource(): void
    {
        $expected = StringHelper::trimSql(require('queries/resource/q1.php'));
        $actual = Consumption::all()
            ->select([
                'counterId' => 'counter',
                'tarifId' => 'tarif',
                'consumption',
            ])
            ->bound(Yaml::query('tests/suql/yaml/resource/LastConsumptionTime.yaml'))
            ->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}
