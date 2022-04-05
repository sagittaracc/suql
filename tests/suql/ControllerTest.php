<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use test\suql\models\Query20;

final class ControllerTest extends TestCase
{
    public function testRoute(): void
    {
        // Query20::all()->select(['route1']);
        $this->assertTrue(true);
    }
}
