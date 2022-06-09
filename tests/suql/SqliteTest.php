<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\db\Container;
use suql\syntax\Query;
use test\suql\models\Query18;

final class SqliteTest extends TestCase
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

    public function testRealSqliteDb(): void
    {
        $tmp = Query18::all()
            ->select([
                'f1' => 'af1',
                'f2' => 'af2',
            ])
            ->fetchAll();
        $this->assertEquals([
            ['af1' => '1', 'af2' => '1'],
            ['af1' => '2', 'af2' => '2'],
            ['af1' => '3', 'af2' => '3'],
        ], $tmp);
    }

    public function testMixDifferentDatabases(): void
    {
        // TODO: Написать этот тест - использование вместе Sqlite и MySql
        $this->assertTrue(true);
    }
}
