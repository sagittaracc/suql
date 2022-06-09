<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\db\Container;
use suql\syntax\Query;
use test\suql\models\Query10;
use test\suql\models\Query18;

final class BufferTest extends TestCase
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

    public function testBuffer(): void
    {
        $expected = [
            ['f1' => '1'],
            ['f1' => '2'],
        ];

        $actual = Query18::all()
            ->select(['f1', 'f2'])
            ->buff()
            ->join(Query10::class)
            ->select(['f1'])
            ->fetchAll();
        
        $this->assertSame($expected, $actual);
    }
}
