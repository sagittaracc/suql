<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\builder\SQLDriver;
use suql\core\SuQLObject;
use suql\core\SuQLScheme;

class SuQLMock extends TestCase
{
    protected $osuql;

    protected function setUp(): void
    {
        $scheme = new SuQLScheme();
        $driver = new SQLDriver('mysql');

        $this->osuql = new SuQLObject($scheme, $driver);
    }

    protected function tearDown(): void
    {
        $this->osuql = null;
    }
}