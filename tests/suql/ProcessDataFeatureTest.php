<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\db\Container;
use suql\syntax\Query;
use test\suql\models\Query10;
use test\suql\models\Query9;

final class ProcessDataFeatureTest extends TestCase
{
    public function setUp(): void
    {
        // Создаем базу данных db_test
        Container::create(require('config/db.php'));
        Query::create('create database db_test')->setConnection('connection')->exec();
        // Создаем таблицу table_1
        Container::add(require('config/db-test.php'));
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

    public function testPostProcessFunction(): void
    {
        // не используя функцию пост обработки
        $data = Query10::all()->fetchAll();
        $this->assertEquals([
            ['f1' => '1', 'f2' => '1'],
            ['f1' => '2', 'f2' => '2'],
            ['f1' => '3', 'f2' => '3'],
            ['f1' => '4', 'f2' => '4'],
            ['f1' => '5', 'f2' => '5'],
            ['f1' => '6', 'f2' => '6'],
        ], $data);

        // используя функцию пост обработки
        $data = Query10::all()->castF1ToInt()->fetchAll();
        $this->assertEquals([
            ['f1' => 1, 'f2' => '1'],
            ['f1' => 2, 'f2' => '2'],
            ['f1' => 3, 'f2' => '3'],
            ['f1' => 4, 'f2' => '4'],
            ['f1' => 5, 'f2' => '5'],
            ['f1' => 6, 'f2' => '6'],
        ], $data);
    }
}
