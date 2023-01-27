<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\syntax\PhpInput;
use test\suql\routes\App;
use test\suql\routes\MyApp;

final class RouteTest extends TestCase
{
    public function testRoute(): void
    {
        $app = new App();
        $actual = $app->run('/site/main/1/yuriy');
        $expected = 'I am working with an element by id 1 and its name is yuriy';
        $this->assertEquals($expected, $actual);
    }

    public function testFailRoute(): void
    {
        $this->expectExceptionMessage('404');
        $app = new App();
        $app->run('/site/main/fail');
    }

    public function testRpc(): void
    {
        $mock = $this->getMockBuilder(PhpInput::class)->setMethods(['get'])->getMock();
        $mock->expects($this->any())->method('get')->will($this->returnValue('{"json-rpc": "2.0", "method": "foo", "params": {"a": "a", "b": "b"}}'));

        $phpInput = new PhpInput();
        var_dump($phpInput->get());

        // $app = new MyApp();
        // $app->run();
    }
}