<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\annotation\RelationAnnotation;
use suql\annotation\RouteAnnotation;
use suql\annotation\TableAnnotation;
use test\suql\models\Groups;
use test\suql\models\GroupsNullTable;
use test\suql\models\Query20;
use test\suql\models\Users;

final class AnnotationTest extends TestCase
{
    public function testTableAnnotation(): void
    {
        $annotation = TableAnnotation::from(Groups::class)->read();
        $this->assertEquals('groups', $annotation->table);

        $annotation = TableAnnotation::from(GroupsNullTable::class)->read();
        $this->assertNull($annotation->table);
    }

    public function testRelationAnnotation(): void
    {
        $annotation = RelationAnnotation::from(Users::class)->for('products')->read();
        $this->assertEquals('hasMany', $annotation->relation);
        $this->assertEquals('test\suql\models\Products', $annotation->second_model);
        $this->assertEquals('products', $annotation->second_table);
        $this->assertEquals('id', $annotation->second_field);
        $this->assertEquals('product_id', $annotation->first_field);
    }

    public function testRouteAnnotation(): void
    {
        $annotation = RouteAnnotation::from(Query20::class)->for('GET', 'some/route')->read();
        $this->assertEquals('routeHandler', $annotation->function);
    }

    public function testJoinByRelationAnnotation(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q30.php'));
        $actual = Users::all()->join('products')->join('categories')->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}
