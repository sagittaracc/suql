<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\syntax\SuQL1;
use Symfony\Component\Yaml\Yaml;
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
                'consumption' => 'consumption',
            ])
            ->bound(SuQL1::query('tests/suql/yaml/resource/LastConsumptionTime.yaml', Yaml::class))
            ->getRawSql();
        $this->assertEquals($expected, $actual);
    }

    public function testInnerYaml(): void
    {
        $expected = StringHelper::trimSql(require('queries/resource/q1.php'));
        $actual = SuQL1::query('tests/suql/yaml/resource/LastConsumption.yaml', Yaml::class)->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}
