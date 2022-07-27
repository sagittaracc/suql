<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\syntax\SuQL;

final class TemplateTest extends TestCase
{
    public function testSgClick(): void
    {
        $expected =
            '<div id="app">'.
                '<label class="count"></label>'.
                '<button onclick="app.likeIt()">Like Button</button>'.
            '</div>'.
            '<script type="text/javascript">'.
                'window.app = {'.
                    'likes: {'.
                        'path: \'app>count\','.
                        'value: undefined'.
                    '}'.
                '}'.
            '</script>';
        $actual = SuQL::template('tests/suql/templates/Template1.tsml');
        $this->assertEquals($expected, $actual);
    }

    public function testSgModel(): void
    {
        $expected =
            '<div id="app">'.
                '<label class="input"></label>'.
                '<input onkeyup="assign(app.text, this.value)"></input>'.
            '</div>'.
            '<script type="text/javascript">'.
                'window.app = {'.
                    'text: {'.
                        'path: \'app>input\','.
                        'value: undefined'.
                    '}'.
                '}'.
            '</script>';
        $actual = SuQL::template('tests/suql/templates/Template2.tsml');
        $this->assertEquals($expected, $actual);
    }
}