<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\db\Container;
use suql\manager\EntityManager;
use suql\syntax\Query;
use test\suql\models\Query11;

final class EntityManagerTest extends TestCase
{
    public function setUp(): void
    {
        // Create a database
        Container::create(require('config/db.php'));
        Query::create('create database db_test')->setConnection('connection')->exec();
        Container::add(require('config/db-test.php'));
        Query::create('create table table_10(f1 int, f2 int, primary key (f1))')->setConnection('db_test')->exec();
        Query::create('insert into table_10(f1, f2) values (1, 1), (2, 2), (3, 3)')->setConnection('db_test')->exec();
        Query::create('insert into table_10(f1, f2) values (?, ?), (?, ?), (?, ?)')->setConnection('db_test')->exec([4, 4, 5, 5, 6, 6]);
    }

    public function tearDown(): void
    {
        // Drop the database
        Query::create('drop table table_10')->setConnection('db_test')->exec();
        Query::create('drop database db_test')->setConnection('db_test')->exec();
    }

    public function testPersist(): void
    {
        $entity = new Query11();
        $entity->f1 = 7;
        $entity->f2 = 7;

        $entityManager = new EntityManager();
        $entityManager->persist($entity);
        $entityManager->run();

        $justAddedRow = Query11::all()->where(['f1' => 7])->fetchOne();
        $this->assertEquals(7, $justAddedRow->f2);
    }
}
