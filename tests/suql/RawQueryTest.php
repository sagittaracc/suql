<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\db\Container;
use suql\syntax\Query;
use test\suql\models\Query10;

final class RawQueryTest extends TestCase
{
    public function setUp(): void
    {
        // Создаем базу данных db_test
        Container::create(require('config/db-null.php'));
        Query::create('create database db_test')->setConnection('connection')->exec();
        // Создаем таблицу table_1
        Container::add(require('config/db.php'));
        Query::create('create table table_10(f1 int, f2 int)')->setConnection('db_test')->exec();
        // Добавляем в неё три записи
        Query::create('insert into table_10 (f1, f2) values (1, 1), (2, 2), (3, 3)')->setConnection('db_test')->exec();
        // Добавляем еще три записи
        Query::create('insert into table_10 (f1, f2) values (?, ?), (?, ?), (?, ?)')->setConnection('db_test')->exec([4, 4, 5, 5, 6, 6]);
    }

    public function tearDown(): void
    {
        // Удаляем таблицу и базу данных
        Query::create('drop table table_10')->setConnection('db_test')->exec();
        Query::create('drop database db_test')->setConnection('db_test')->exec();
    }

    public function testDeleteQuery(): void
    {
        // Удаляем все записи из таблицы и сверяем количество удаленных записей
        $count = Query::create('delete from table_10')->setConnection('db_test')->exec();
        $this->assertEquals(6, $count);
    }

    public function testQueryBindMethod(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q20.php'));

        $actual = Query::create('select * from ?')->bind([Query10::all()])->getQuery();
        $this->assertEquals($expected, $actual);

        $actual = Query::create('select * from :query')->bind([':query' => Query10::all()])->getQuery();
        $this->assertEquals($expected, $actual);
    }

    public function testNotSetConnection(): void
    {
        $this->expectExceptionMessage('Connection is not set!');
        Query::create('delete from table_10')->exec();
    }
}
