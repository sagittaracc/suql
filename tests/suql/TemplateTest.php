<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\syntax\SuQL;

final class TemplateTest extends TestCase
{
    public function testSuQLTemplate(): void
    {
        $expected =
            '<div id="app">'.
                '<label id="count"></label>'.
                '<button onclick="app.likeIt()">Like</button>'.
            '</div>'.
            '<script type="text/javascript">'.
                'window.app = {'.
                    'likes: {'.
                        'path: \'count\','.
                        'value: undefined'.
                    '}'.
                '}'.
            '</script>';
        $actual = SuQL::template('tests/suql/templates/Template1.tsml');
        $this->assertEquals($expected, $actual);
    }
}
