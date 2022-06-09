<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\db\Container;
use suql\syntax\Query;
use test\suql\models\Query24;
use test\suql\models\Query25;
use test\suql\models\Query26;

final class ArrayTest extends TestCase
{
    public function setUp(): void
    {
        // Create a database
        Container::create(require('config/db-null.php'));
        Query::create('create database db_test')->setConnection('connection')->exec();
        Container::add(require('config/db.php'));
        Query::create('create table table_26(f1 int, f2 int, primary key (f1))')->setConnection('db_test')->exec();
        Query::create('insert into table_26 (f1, f2) values (1, 1), (2, 2), (3, 3)')->setConnection('db_test')->exec();
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

    public function testJoinArrayWithArray(): void
    {
        $expected = [
            ['id' => '1', 'user' => 'user1', 'pass' => 'pass1', 'user_id' => '1', 'login' => 'login1'],
            ['id' => '2', 'user' => 'user2', 'pass' => 'pass2', 'user_id' => '2', 'login' => 'login2'],
        ];
        $actual = Query24::all()->join(Query25::class)->fetchAll();
        $this->assertEquals($expected, $actual);
    }

    public function testJoinArrayWithTable(): void
    {
        $expected = [
            ['id' => '1', 'user' => 'user1', 'pass' => 'pass1', 'f1' => '1', 'f2' => '1'],
            ['id' => '2', 'user' => 'user2', 'pass' => 'pass2', 'f1' => '2', 'f2' => '2'],
        ];
        $actual = Query24::all()->join(Query26::class)->fetchAll();
        $this->assertEquals($expected, $actual);
    }

    public function testJoinTableWithArray(): void
    {
        $expected = [
            ['id' => '1', 'user' => 'user1', 'pass' => 'pass1', 'f1' => '1', 'f2' => '1'],
            ['id' => '2', 'user' => 'user2', 'pass' => 'pass2', 'f1' => '2', 'f2' => '2'],
        ];
        $actual = Query26::all()->join(Query24::class)->fetchAll();
        $this->assertEquals($expected, $actual);
    }
}
