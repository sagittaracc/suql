<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\manager\ControllerEntityManager;
use test\suql\models\Query20;

final class ControllerEntityManagerTest extends TestCase
{
    public function testRoute(): void
    {
        $route = Query20::all()->select('some/route');

        $manager = new ControllerEntityManager();
        $data = $manager->fetch($route);

        $this->assertEquals(['foo' => 'bar'], $data);
    }

    public function testAnotherRoute(): void
    {
        $route = Query20::all()->select('raw/sql');

        $manager = new ControllerEntityManager();
        $rawsql = $manager->fetch($route);

        $this->assertEquals(StringHelper::trimSql(require('queries/mysql/q1.php')), $rawsql);
    }
}
