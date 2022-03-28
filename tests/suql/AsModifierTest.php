<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\syntax\field\Field;
use test\suql\models\Query1;

final class AsModifierTest extends TestCase
{
    public function testAsModifier(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q4.php'));
        $actual = Query1::all()
            ->select([
                new Field('f1', [
                    'as' => ['af1'],
                ]),
                new Field('f2', [
                    'as' => ['af2'],
                ]),
            ])
            ->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}
