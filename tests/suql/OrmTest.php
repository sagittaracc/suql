<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\db\Container;
use suql\syntax\Query;
use test\suql\models\T1;
use test\suql\models\T2;

final class OrmTest extends TestCase
{
    public function setUp(): void
    {
        // Create a database
        Container::create(require('config/db-null.php'));
        Query::create('create database db_test')->setConnection('connection')->exec();
        Container::add(require('config/db.php'));

        Query::create('create table ot1(a1 int, a2 int, primary key (a1))')->setConnection('db_test')->exec();
        Query::create('insert into ot1(a1, a2) values (1, 1), (2, 2), (3, 3)')->setConnection('db_test')->exec();

        Query::create('create table ot2(b1 int, b2 int, primary key (b1))')->setConnection('db_test')->exec();
        Query::create('insert into ot2(b1, b2) values (1, 1), (3, 3)')->setConnection('db_test')->exec();

        Query::create('create table ot3(c1 int, c2 int, primary key (c1))')->setConnection('db_test')->exec();
        Query::create('insert into ot3(c1, c2) values (2, 2), (3, 3)')->setConnection('db_test')->exec();
    }

    public function tearDown(): void
    {
        // Drop the database
        Query::create('drop table ot1')->setConnection('db_test')->exec();
        Query::create('drop table ot2')->setConnection('db_test')->exec();
        Query::create('drop table ot3')->setConnection('db_test')->exec();
        Query::create('drop database db_test')->setConnection('db_test')->exec();
    }

    public function testOrm(): void
    {
        $t2Data = T1::all()->getT2();
        foreach ($t2Data as $t2Object) {
            $t3Data = $t2Object->getT3();
            foreach ($t3Data as $t3Object) {
                $this->assertEquals(3, $t3Object->c1);
                $this->assertEquals(3, $t3Object->c2);
            }
        }
        $this->assertTrue(true);
    }

    public function testOrmAfterJoin(): void
    {
        $t3Data = T1::all()->join(T2::class)->getT3();
        foreach ($t3Data as $t3Object) {
            $this->assertEquals(3, $t3Object->c1);
            $this->assertEquals(3, $t3Object->c2);
        }
        $this->assertTrue(true);
    }

    public function testMainLastRequestedModel(): void
    {
        $query = T1::all();
        $this->assertEquals('test\suql\models\T1', $query->getLastRequestedModel());
    }

    public function testLastRequestedModel(): void
    {
        $query = T1::all();
        $query->getT2();

        $this->assertEquals('test\suql\models\T2', $query->getLastRequestedModel());
    }
}
