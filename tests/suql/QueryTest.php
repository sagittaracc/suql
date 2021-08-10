<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\db\Container;
use suql\syntax\Query;
use suql\syntax\Transaction;
use test\suql\models\TableName;
use test\suql\models\TableNameWithFields;
use test\suql\models\TempTable;
use test\suql\models\User;

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

    public function testTableNameWithFields(): void
    {
        $tn1 = TableNameWithFields::new();
        $tn1->field = 7;
        $tn1->another_field = 7;
        $tn1->save();

        // TODO: Исправить. Не работает fetcOne. Ошибка в syntax\SuQL.php line 511. Когда выбирается только один элемент
        // он не массив по которому мы итерируем
        $justAddedRow = TableNameWithFields::all()->where(['field' => 7])->fetchAll();
        $this->assertEquals(7, $justAddedRow[0]->field);

        $data = TableNameWithFields::all()->fetchAll();
        foreach ($data as $index => $row) {
            $this->assertInstanceOf(TableNameWithFields::class, $row);
            $this->assertEquals($index + 1, $row->field);
        }
    }

    public function testJoinWithPHPArray(): void
    {
        $tableData = [
            ['id' => 1, 'name' => 'mario'],
            ['id' => 2, 'name' => 'fayword'],
            ['id' => 3, 'name' => '1nterFucker'],
        ];

        $query = TempTable::load($tableData)->getTableName();

        $this->assertEquals([
            ['id' => '1', 'name' => 'mario', 'field' => '1', 'another_field' => '1'],
            ['id' => '2', 'name' => 'fayword', 'field' => '2', 'another_field' => '2'],
            ['id' => '3', 'name' => '1nterFucker', 'field' => '3', 'another_field' => '3'],
        ], $query->fetchAll());
    }

    public function testPostSuQLFunction(): void
    {
        $data = TableName::all()->toInt()->fetchAll();
        $this->assertEquals([
            ['field' => 1, 'another_field' => '1'],
            ['field' => 2, 'another_field' => '2'],
            ['field' => 3, 'another_field' => '3'],
            ['field' => 4, 'another_field' => '4'],
            ['field' => 5, 'another_field' => '5'],
            ['field' => 6, 'another_field' => '6'],
        ], $data);
    }

    public function testFetchAllAndOne(): void
    {
        $data = TableName::all()->select(['field'])->fetchAll();
        $firstRow = TableName::all()->select(['field'])->order(['field' => 'desc'])->fetchOne();

        $this->assertEquals([
            ['field' => '1'],
            ['field' => '2'],
            ['field' => '3'],
            ['field' => '4'],
            ['field' => '5'],
            ['field' => '6'],
        ], $data);
        $this->assertEquals([
            'field' => 6,
        ], $firstRow);
    }

    public function testDeleteQuery(): void
    {
        $count = Query::create('delete from table_name')->setConnection('db_test')->exec();

        $this->assertEquals(6, $count);
    }

    public function testQueryInjection(): void
    {
        $query = Query::create('select * from ?')->bind([User::all()])->getQuery();

        $this->assertEquals('select * from (select * from users)', $query);
    }

    public function testSuccessTransaction(): void
    {
        $success = false;
        $db = Query::create()->setConnection('db_test');

        try {
            $transaction = Transaction::begin($db);
            $db->query("insert into table_name (field, another_field) values (100, 100)")->exec();
            $db->query("insert into table_name (field, another_field) values (101, 101)")->exec();
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
            $db->query("insert into table_name (field, another_field) values (100, 100)")->exec();
            $db->query("insert into table_name (field, another_field, third_field) values (101, 'string', false)")->exec();
            $success = true;
            $transaction->commit();
        } catch (Exception $e) {
            $success = false;
            $transaction->rollback();
        }

        $this->assertFalse($success);
    }
}
