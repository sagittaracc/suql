<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\manager\Controller;
use test\suql\models\Query20;

final class ControllerTest extends TestCase
{
    public function testRoute(): void
    {
        $query = Query20::all()->select(['route1']);

        $manager = new Controller();
        $manager->persist($query);
        $data = $manager->run();

        $this->assertEquals(['foo' => 'bar'], $data);
    }
}
