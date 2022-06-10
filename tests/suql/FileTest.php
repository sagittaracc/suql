<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\db\Container;
use suql\syntax\Query;
use test\suql\models\Query26;
use test\suql\models\Query28;

final class FileTest extends TestCase
{
    public function setUp(): void
    {
        // Create a database
        Container::create(require('config/db-null.php'));
        Query::create('create database db_test')->setConnection('connection')->exec();
        Container::add(require('config/db.php'));
        Query::create('create table table_26(f1 int, f2 int, primary key (f1))')->setConnection('db_test')->exec();
        Query::create('insert into table_26 (f1, f2) values (1, 1), (2, 2)')->setConnection('db_test')->exec();
    }

    public function tearDown(): void
    {
        // Drop the database
        Query::create('drop database db_test')->setConnection('db_test')->exec();
    }

    public function testFile(): void
    {
        $expected = [
            ['f1' => '1', 'f2' => '1'],
            ['f1' => '2', 'f2' => '2'],
            ['f1' => '3', 'f2' => '3'],
        ];
        $actual = Query28::find(['f1', 'f2'])->fetchAll();
        $this->assertSame($expected, $actual);
    }

    public function testJoinFileWithTable(): void
    {
        $expected = [
            ['f1' => '1', 'f2' => '1'],
            ['f1' => '2', 'f2' => '2'],
        ];
        $actual = Query28::find(['f1', 'f2'])->join(Query26::class)->fetchAll();
        $this->assertSame($expected, $actual);
    }
}
