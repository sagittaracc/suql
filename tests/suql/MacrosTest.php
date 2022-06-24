<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\models\Query30;

final class MacrosTest extends TestCase
{
    public function testMacros(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q42.php'));
        $actual = Query30::all()->empty('f1')->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}
