<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\db\Container;
use suql\syntax\Query;
use test\suql\models\Query24;
use test\suql\models\Query25;

final class ArrayTest extends TestCase
{
    public function setUp(): void
    {
        // Create a database
        Container::create(require('config/db.php'));
        Query::create('create database db_test')->setConnection('connection')->exec();
        Container::add(require('config/db-test.php'));
    }

    public function tearDown(): void
    {
        // Drop the database
        Query::create('drop database db_test')->setConnection('db_test')->exec();
    }

    public function testArray(): void
    {
        $expected = [
            ['id' => 1, 'user' => 'user1', 'pass' => 'pass1'],
            ['id' => 2, 'user' => 'user2', 'pass' => 'pass2'],
        ];
        $actual = Query24::all()->fetchAll();
        $this->assertEquals($expected, $actual);
    }
}
