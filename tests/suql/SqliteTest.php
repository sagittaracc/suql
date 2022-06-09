<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\db\Container;
use suql\syntax\Query;
use test\suql\models\buffers\Buffer;
use test\suql\models\Query10;
use test\suql\models\Query18;

final class SqliteTest extends TestCase
{
    public function setUp(): void
    {
        // Create a database
        Container::create(require('config/db-null.php'));
        Query::create('create database db_test')->setConnection('connection')->exec();
        Container::add(require('config/db.php'));
        Query::create('create table table_10(f1 int, f2 int)')->setConnection('db_test')->exec();
        Query::create('insert into table_10 (f1, f2) values (1, 1), (2, 2)')->setConnection('db_test')->exec();
    }

    public function tearDown(): void
    {
        // Drop the database
        Query::create('drop database db_test')->setConnection('db_test')->exec();
    }

    public function testRealSqliteDb(): void
    {
        $expected = [
            ['af1' => '1', 'af2' => '1'],
            ['af1' => '2', 'af2' => '2'],
            ['af1' => '3', 'af2' => '3'],
        ];
        $actual = Query18::all()
            ->select([
                'f1' => 'af1',
                'f2' => 'af2',
            ])
            ->fetchAll();

        $this->assertEquals($expected, $actual);
    }

    public function testMixDifferentDatabases(): void
    {
        $expected = [
            ['f1' => '1'],
            ['f1' => '2'],
        ];

        Buffer::load(
            Query18::all()
                ->select(['f1', 'f2'])
                ->fetchAll()
        );
        $actual = Buffer::all()->join(Query10::class)->select(['f1'])->fetchAll();
        
        $this->assertSame($expected, $actual);
    }
}
