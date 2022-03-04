<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\models\Query6;

final class NestedQueryTest extends TestCase
{
    public function testNestedQuery(): void
    {
        $expected = StringHelper::trimSql(require('queries/q14.php'));
        $actual = Query6::all()->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}