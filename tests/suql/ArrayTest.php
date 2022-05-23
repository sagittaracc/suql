<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use test\suql\models\Query24;

final class ArrayTest extends TestCase
{
    public function testArray(): void
    {
        $expected = [
            ['id' => 1, 'user' => 'user1', 'pass' => 'pass1'],
            ['id' => 2, 'user' => 'user2', 'pass' => 'pass2'],
        ];
        $actual = Query24::all()->fetchAll();
        $this->assertEquals($expected, $actual);
    }
}
