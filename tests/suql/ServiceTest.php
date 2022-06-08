<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\db\Container;
use suql\syntax\Query;
use test\suql\models\Query26;
use test\suql\models\Query27;

final class ServiceTest extends TestCase
{
    public function setUp(): void
    {
        // Create a database
        Container::create(require('config/db.php'));
        Query::create('create database db_test')->setConnection('connection')->exec();
        Container::add(require('config/db-test.php'));
        Query::create('create table table_26(f1 int, f2 int, primary key (f1))')->setConnection('db_test')->exec();
        Query::create('insert into table_26 (f1, f2) values (1, 1), (2, 2), (3, 3)')->setConnection('db_test')->exec();
    }

    public function tearDown(): void
    {
        // Drop the database
        Query::create('drop database db_test')->setConnection('db_test')->exec();
    }

    public function testService(): void
    {
        $expected = Query27::find(['userId' => 2])->select(['userId', 'id', 'title'])->join(Query26::class)->select(['f2'])->fetchOne();
        $actual = [
            'userId' => '2',
            'id' => '11',
            'title' => 'et ea vero quia laudantium autem',
            'f2' => '2',
        ];
        $this->assertSame($expected, $actual);
    }
}
