<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use test\suql\models\relational\Categories;
use test\suql\models\relational\Products;
use test\suql\models\relational\Users;

final class RelationTest extends TestCase
{
    public function testRelation(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q30.php'));
        $actual = Users::all()->join(Products::class)->join(Categories::class)->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}
