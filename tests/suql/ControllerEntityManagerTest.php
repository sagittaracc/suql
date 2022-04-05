<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\manager\ControllerEntityManager;
use test\suql\models\Query20;

final class ControllerEntityManagerTest extends TestCase
{
    public function testRoute(): void
    {
        $route = Query20::all()->select('route1');

        $manager = new ControllerEntityManager();
        $manager->persist($route);
        $data = $manager->run();

        $this->assertEquals(['foo' => 'bar'], $data);
    }
}
