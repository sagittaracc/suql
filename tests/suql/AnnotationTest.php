<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\models\Query20;

final class AnnotationTest extends TestCase
{
    public function testAnnotation(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q30.php'));
        $actual = Query20::all()->join('table_21')->getQuery22()->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}
