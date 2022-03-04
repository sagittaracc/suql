<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\db\Container;
use suql\syntax\Query;
use test\suql\models\TableName;
use test\suql\schema\AppScheme;

final class QueryTest extends TestCase
{
    public function setUp(): void
    {
        // Create a database
        Container::create(require('config/db.php'));
        Query::create('create database db_test')->setConnection('connection')->exec();
        Container::add(require('config/db-test.php'));
        Query::create('create table table_name(field int, another_field int)')->setConnection('db_test')->exec();
        Query::create('insert into table_name (field, another_field) values (1, 1), (2, 2), (3, 3)')->setConnection('db_test')->exec();
        Query::create('insert into table_name (field, another_field) values (?, ?), (?, ?), (?, ?)')->setConnection('db_test')->exec([4, 4, 5, 5, 6, 6]);
    }

    public function tearDown(): void
    {
        // Drop the database
        Query::create('drop table table_name')->setConnection('db_test')->exec();
        Query::create('drop database db_test')->setConnection('db_test')->exec();
    }

    public function testFetchAll(): void
    {
        $data = TableName::all()->select(['field'])->fetchAll();

        $this->assertEquals([
            ['field' => '1'],
            ['field' => '2'],
            ['field' => '3'],
            ['field' => '4'],
            ['field' => '5'],
            ['field' => '6'],
        ], $data);
    }

    public function testFetchOne(): void
    {
        $data = TableName::all()->select(['field'])->order(['field' => 'desc'])->fetchOne();
        $this->assertEquals(['field' => 6], $data);
    }

    public function testDbManagerWithRealDatabase(): void
    {
        $db = new suql\db\Manager('db_test', AppScheme::class);
        $data = $db->entity('table_name')->select(['field'])->fetchAll();
        $this->assertEquals([
            ['field' => '1'],
            ['field' => '2'],
            ['field' => '3'],
            ['field' => '4'],
            ['field' => '5'],
            ['field' => '6'],
        ], $data);
    }
}
