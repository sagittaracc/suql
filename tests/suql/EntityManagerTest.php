<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\db\Container;
use suql\manager\EntityManager;
use suql\syntax\Query;
use test\suql\models\Query11;
use test\suql\models\Query3;

final class EntityManagerTest extends TestCase
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

    public function testPersist(): void
    {
        $entity = new Query11();
        $entity->f2 = 1;

        $entityManager = new EntityManager();
        $entityManager->persist($entity);
        $entityManager->run();

        $this->assertEquals(1, $entity->getLastInsertId());
    }

    public function testPersistWithSubEntity(): void
    {
        $subEntity = new Query3();
        $subEntity->f2 = 1;

        $entity = new Query11();
        $entity->f2 = $subEntity;

        $entityManager = new EntityManager();
        $entityManager->persist($entity);
        $entityManager->run();

        $this->assertEquals(1, $subEntity->getLastInsertId());
        $this->assertEquals(1, $entity->getLastInsertId());
        $this->assertEquals(1, Query11::one(1)->f2);
    }
}
