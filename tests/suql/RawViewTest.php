<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\db\Container;
use suql\syntax\Query;
use test\suql\models\Query17;

final class RawViewTest extends TestCase
{
    public function setUp(): void
    {
        // Create a database
        Container::create(require('config/db-null.php'));
        Query::create('create database db_test')->setConnection('connection')->exec();
        Container::add(require('config/db.php'));
        Query::create('create table table_10(f1 int, f2 int)')->setConnection('db_test')->exec();
        Query::create('insert into table_10 (f1, f2) values (1, 1), (2, 2), (3, 3)')->setConnection('db_test')->exec();
        Query::create('insert into table_10 (f1, f2) values (?, ?), (?, ?), (?, ?)')->setConnection('db_test')->exec([4, 4, 5, 5, 6, 6]);
    }

    public function tearDown(): void
    {
        // Drop the database
        Query::create('drop table table_10')->setConnection('db_test')->exec();
        Query::create('drop view if exists view_17')->setConnection('db_test')->exec();
        Query::create('drop database db_test')->setConnection('db_test')->exec();
    }

    public function testView(): void
    {
        $data = Query17::all()->fetchAll();
        $this->assertEquals([
            ['f1' => '2'],
            ['f1' => '4'],
            ['f1' => '6'],
        ], $data);
    }
}
