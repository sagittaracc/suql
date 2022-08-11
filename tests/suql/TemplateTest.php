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
                    '"likes":{'.
                        '"value":null,'.
                        '"paths":{'.
                            '"app>count":{'.
                                '"format":"raw",'.
                                '"template":null'.
                            '}'.
                        '}'.
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
                '<input onkeyup="assign(app.text, this.value)" class="my-input"></input>'.
            '</div>'.
            '<script type="text/javascript">'.
                'window.app = {'.
                    '"text":{'.
                        '"value":null,'.
                        '"paths":{'.
                            '"app>input":{'.
                                '"format":"raw",'.
                                '"template":null'.
                            '},'.
                            '"app>my-input":{'.
                                '"format":"value",'.
                                '"template":null'.
                            '}'.
                        '}'.
                    '}'.
                '}'.
            '</script>';
        $actual = SuQL::template('tests/suql/templates/Template2.tsml');
        $this->assertEquals($expected, $actual);
    }

    public function testSgForeach(): void
    {
        $expected =
            '<div id="app">'.
                '<ul class="task-list"></ul>'.
            '</div>'.
            '<script type="text/javascript">'.
                'window.app = {'.
                    '"taskList":{'.
                        '"value":null,'.
                        '"paths":{'.
                            '"app>task-list":{'.
                                '"format":"html",'.
                                '"template":"<li><span>[[id]]<\/span>[[name]]<\/li>"'.
                            '}'.
                        '}'.
                    '}'.
                '}'.
            '</script>';
        $actual = SuQL::template('tests/suql/templates/Template3.tsml');
        $this->assertEquals($expected, $actual);
    }
}
