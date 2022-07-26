<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\syntax\SuQL;

final class TemplateTest extends TestCase
{
    public function testTsmlSuQLParse(): void
    {
        // $expected = [
        //     'div@app' => [
        //         'label' => [
        //             '{{$likes}}' => []
        //         ],
        //         'button' => [
        //             'sg-click' => 'likeIt'
        //         ]
        //     ]
        // ];
        $expected =
            '<div id="app">'.
                '<label id="label-1"></label>'.
                '<button id="button-2"></button>'.
            '</div>'.
            '<script type="text/javascript">'.
                'window.app = {'.
                    'likes: {'.
                        'path: \'#label-1\','.
                        'value: undefined'.
                    '}'.
                '}'.
            '</script>';
        $actual = SuQL::template('tests/suql/templates/Template1.tsml');
        $this->assertEquals($expected, $actual);
    }
}
