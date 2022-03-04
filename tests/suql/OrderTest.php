<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\models\Query1;

final class OrderTest extends TestCase
{
    /**
     * Example:
     * 
     * select * from table order by id
     * 
     */
    public function testSelectOrder(): void
    {
        $expected = StringHelper::trimSql(require('queries/q6.php'));
        $actual = Query1::all()->order([
            'f1' => 'desc',
            'f2' => 'asc',
        ])->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}