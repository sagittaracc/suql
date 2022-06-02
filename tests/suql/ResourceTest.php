<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\syntax\parser\Yaml;
use suql\syntax\SuQL;
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
            ->bound(SuQL::query('tests/suql/yaml/resource/LastConsumptionTime.yaml', new Yaml))
            ->getRawSql();
        $this->assertEquals($expected, $actual);
    }

    public function testInnerYaml(): void
    {
        $expected = StringHelper::trimSql(require('queries/resource/q1.php'));
        $actual = SuQL::query('tests/suql/yaml/resource/LastConsumption.yaml', new Yaml)->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}
