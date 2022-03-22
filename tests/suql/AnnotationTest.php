<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\syntax\Annotation;
use test\suql\models\Users;

final class AnnotationTest extends TestCase
{
    public function testReadAnnotation(): void
    {
        $annotation = Annotation::from(Users::class)->for('products')->read();
        $this->assertEquals('hasMany', $annotation->relation);
        $this->assertEquals('test\suql\models\Products', $annotation->second_model);
        $this->assertEquals('products', $annotation->second_table);
        $this->assertEquals('id', $annotation->second_field);
        $this->assertEquals('product_id', $annotation->first_field);
    }

    public function testJoinByAnnotation(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q30.php'));
        $actual = Users::all()->getProducts()->join('categories')->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}
