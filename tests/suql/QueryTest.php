<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\db\Container;
use suql\syntax\Query;
use suql\syntax\Transaction;
use test\suql\models\Query1;
use test\suql\models\TableName;
use test\suql\models\TableNameWithFields;
use test\suql\models\TempTable;
use test\suql\models\User;
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

    public function testDbManager(): void
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
