<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use test\suql\models\Query29;

final class JsonServiceTest extends TestCase
{
    public function testJsonService(): void
    {
        $actual = Query29::call('generateIntegers', [
            'apiKey' => '175082e4-b7bb-43cc-a018-df19794bcbb2',
            'n' => 3,
            'min' => 0,
            'max' => 9
        ]);
        foreach ($actual as $number) {
            $this->assertLessThanOrEqual(9, $number);
            $this->assertGreaterThanOrEqual(0, $number);
        }
        $this->assertCount(3, $actual);
    }
}
