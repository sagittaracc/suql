<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use test\suql\models\Query20;

final class ControllerTest extends TestCase
{
    public function testRoute(): void
    {
        $this->assertEquals(['foo' => 'bar'], Query20::all()->go('route1'));
    }
}
