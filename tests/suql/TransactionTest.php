<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\db\Container;
use suql\syntax\Query;
use suql\syntax\Transaction;

final class TransactionTest extends TestCase
{
    public function setUp(): void
    {
        // Создаем базу данных db_test
        Container::create(require('config/db.php'));
        Query::create('create database db_test')->setConnection('connection')->exec();
        // Создаем таблицу table_1
        Container::add(require('config/db-test.php'));
        Query::create('create table table_1(f1 int, f2 int)')->setConnection('db_test')->exec();
        // Добавляем в неё три записи
        Query::create('insert into table_1 (f1, f2) values (1, 1), (2, 2), (3, 3)')->setConnection('db_test')->exec();
        // Добавляем еще три записи
        Query::create('insert into table_1 (f1, f2) values (?, ?), (?, ?), (?, ?)')->setConnection('db_test')->exec([4, 4, 5, 5, 6, 6]);
    }

    public function tearDown(): void
    {
        // Удаляем таблицу и базу данных
        Query::create('drop table table_1')->setConnection('db_test')->exec();
        Query::create('drop database db_test')->setConnection('db_test')->exec();
    }

    public function testSuccessTransaction(): void
    {
        $success = false;
        $db = Query::create()->setConnection('db_test');

        try {
            $transaction = Transaction::begin($db);
            $db->query("insert into table_1 (f1, f2) values (100, 100)")->exec();
            $db->query("insert into table_1 (f1, f2) values (101, 101)")->exec();
            $success = true;
            $transaction->commit();
        } catch (Exception $e) {
            $success = false;
            $transaction->rollback();
        }

        $this->assertTrue($success);
    }

    public function testFailTransaction(): void
    {
        $success = false;
        $db = Query::create()->setConnection('db_test');

        try {
            $transaction = Transaction::begin($db);
            $db->query("insert into table_1 (f1, f2) values (100, 100)")->exec();
            $db->query("insert into table_1 (f1, f2, unknown_field) values (101, 'string', false)")->exec();
            $success = true;
            $transaction->commit();
        } catch (Exception $e) {
            $success = false;
            $transaction->rollback();
        }

        $this->assertFalse($success);
    }
}
