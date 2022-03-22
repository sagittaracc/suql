<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\models\Query20;
use test\suql\models\Users;

final class AnnotationTest extends TestCase
{
    public function testAnnotation(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q30.php'));
        $actual = Users::all()->getProducts()->join('categories')->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}
