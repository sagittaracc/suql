<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\db\Container;
use suql\syntax\Query;
use test\suql\models\Query19;

final class TriggerTest extends TestCase
{
    public function setUp(): void
    {
        // Create a database
        Container::create(require('config/db-null.php'));
        Query::create('create database db_test')->setConnection('connection')->exec();
        Container::add(require('config/db.php'));
    }

    public function tearDown(): void
    {
        // Drop the database
        Query::create('drop database db_test')->setConnection('db_test')->exec();
    }

    public function testTrigger(): void
    {
        $expected = ['c1' => 7, 'c2' => 'sagittaracc'];

        Query19::trigger('insert', function ($actual) use ($expected) {
            $this->assertEquals($expected, $actual);
        });

        $record = new Query19();
        $record->c1 = 7;
        $record->c2 = 'sagittaracc';
        $record->save();
    }
}
