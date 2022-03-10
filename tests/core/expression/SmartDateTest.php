<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\core\SmartDate;

final class SmartDateTest extends TestCase
{
    private $yesterday;
    private $today;
    private $tomorrow;

    private $dayAgo;
    private $weekAgo;
    private $yearAgo;

    private $lastDay;
    private $lastWeek;
    private $lastYear;

    protected function setUp(): void
    {
        $this->yesterday = SmartDate::create('yesterday');
        $this->today = SmartDate::create('today');
        $this->tomorrow = SmartDate::create('tomorrow');

        $this->dayAgo = SmartDate::create('3 days ago');
        $this->weekAgo = SmartDate::create('3 weeks ago');
        $this->yearAgo = SmartDate::create('3 years ago');

        $this->lastDay = SmartDate::create('last 3 days');
        $this->lastWeek = SmartDate::create('last 3 weeks');
        $this->lastYear = SmartDate::create('last 3 years');
    }

    protected function tearDown(): void
    {
        $this->yesterday = null;
        $this->today = null;
        $this->tomorrow = null;

        $this->dayAgo = null;
        $this->weekAgo = null;
        $this->yearAgo = null;

        $this->lastDay = null;
        $this->lastWeek = null;
        $this->lastYear = null;
    }

    public function testSmartDate(): void
    {
        $this->assertEquals(null, $this->yesterday->getNumber());
        $this->assertEquals('yesterday', $this->yesterday->getPeriod());
        $this->assertEquals('simple', $this->yesterday->getType());

        $this->assertEquals(null, $this->today->getNumber());
        $this->assertEquals('today', $this->today->getPeriod());
        $this->assertEquals('simple', $this->today->getType());

        $this->assertEquals(null, $this->tomorrow->getNumber());
        $this->assertEquals('tomorrow', $this->tomorrow->getPeriod());
        $this->assertEquals('simple', $this->tomorrow->getType());

        $this->assertEquals('3', $this->dayAgo->getNumber());
        $this->assertEquals('day', $this->dayAgo->getPeriod());
        $this->assertEquals('ago', $this->dayAgo->getType());

        $this->assertEquals('3', $this->weekAgo->getNumber());
        $this->assertEquals('week', $this->weekAgo->getPeriod());
        $this->assertEquals('ago', $this->weekAgo->getType());

        $this->assertEquals('3', $this->yearAgo->getNumber());
        $this->assertEquals('year', $this->yearAgo->getPeriod());
        $this->assertEquals('ago', $this->yearAgo->getType());

        $this->assertEquals('3', $this->lastDay->getNumber());
        $this->assertEquals('day', $this->lastDay->getPeriod());
        $this->assertEquals('last', $this->lastDay->getType());

        $this->assertEquals('3', $this->lastWeek->getNumber());
        $this->assertEquals('week', $this->lastWeek->getPeriod());
        $this->assertEquals('last', $this->lastWeek->getType());

        $this->assertEquals('3', $this->lastYear->getNumber());
        $this->assertEquals('year', $this->lastYear->getPeriod());
        $this->assertEquals('last', $this->lastYear->getType());
    }
}