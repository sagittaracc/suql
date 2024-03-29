<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\core\where\Equal;
use suql\db\Container;
use suql\syntax\Query;
use test\suql\models\Query11;

final class InsertTest extends TestCase
{
    public function setUp(): void
    {
        // Create a database
        Container::create(require('config/db-null.php'));
        Query::create('create database db_test')->setConnection('connection')->exec();
        Container::add(require('config/db.php'));
        Query::create('create table table_10(f1 int, f2 int, primary key (f1))')->setConnection('db_test')->exec();
        Query::create('insert into table_10 (f1, f2) values (1, 1), (2, 2), (3, 3)')->setConnection('db_test')->exec();
        Query::create('insert into table_10 (f1, f2) values (?, ?), (?, ?), (?, ?)')->setConnection('db_test')->exec([4, 4, 5, 5, 6, 6]);
    }

    public function tearDown(): void
    {
        // Drop the database
        Query::create('drop table table_10')->setConnection('db_test')->exec();
        Query::create('drop database db_test')->setConnection('db_test')->exec();
    }

    public function testInsert(): void
    {
        $record = new Query11();
        $record->f1 = 7;
        $record->f2 = 7;
        $record->save();

        $justAddedRow = Query11::all()->where(['f1' => Equal::integer(7)])->fetchOne();
        $this->assertEquals(7, $justAddedRow->f2);

        $data = Query11::all()->fetchAll();
        foreach ($data as $index => $row) {
            $this->assertInstanceOf(Query11::class, $row);
            $this->assertEquals($index + 1, $row->f1);
            $this->assertEquals($index + 1, $row->f2);
        }
    }
}
