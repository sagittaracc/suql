<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use test\suql\routes\App;

final class RouteTest extends TestCase
{
    public function testRoute(): void
    {
        $app = new App();
        $actual = $app->run('/site/main/1/yuriy');
        $expected = 'I am working with an element by id 1 and its name is yuriy';
        $this->assertEquals($expected, $actual);
    }
}