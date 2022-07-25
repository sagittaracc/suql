<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\syntax\SuQL;

final class TemplateTest extends TestCase
{
    public function testTsmlSuQLParse(): void
    {
        $expected = [
            'div@app' => [
                'label' => [
                    '{{$likes}}' => []
                ],
                'button' => [
                    'sg-click' => 'likeIt'
                ]
            ]
        ];
        $actual = SuQL::template('tests/suql/templates/Template1.tsml');
        $this->assertEquals($expected, $actual);
    }
}
