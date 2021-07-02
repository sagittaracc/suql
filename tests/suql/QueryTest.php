<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\db\Container;
use suql\syntax\Query;
use test\suql\models\TableName;
use test\suql\models\TableNameWithFields;
use test\suql\models\TempTable;

final class QueryTest extends TestCase
{
    public function setUp(): void
    {
        // Create a database
        Container::create(require('config/db.php'));
        Query::create('connection', 'create database db_test')->exec();
        Container::add(require('config/db-test.php'));
        Query::create('db_test', 'create table table_name(field int)')->exec();
        Query::create('db_test', 'insert into table_name (field) values (1), (2), (3)')->exec();
        Query::create('db_test', 'insert into table_name (field) values (?), (?), (?)')->exec([4, 5, 6]);
    }

    public function tearDown(): void
    {
        // Drop the database
        Query::create('db_test', 'drop table table_name')->exec();
        Query::create('db_test', 'drop database db_test')->exec();
    }

    public function testTableNameWithFields(): void
    {
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
            ['id' => '1', 'name' => 'mario', 'field' => '1'],
            ['id' => '2', 'name' => 'fayword', 'field' => '2'],
            ['id' => '3', 'name' => '1nterFucker', 'field' => '3'],
        ], $query->fetchAll());
    }

    public function testPostSuQLFunction(): void
    {
        $data = TableName::all()->toInt()->fetchAll();
        $this->assertEquals([
            ['field' => 1],
            ['field' => 2],
            ['field' => 3],
            ['field' => 4],
            ['field' => 5],
            ['field' => 6],
        ], $data);
    }

    public function testFetchAllAndOne(): void
    {
        $data = TableName::all()->fetchAll();
        $firstRow = TableName::all()->order(['field' => 'desc'])->fetchOne();
        
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
        $count = Query::create('db_test', 'delete from table_name')->exec();

        $this->assertEquals(6, $count);
    }
}
