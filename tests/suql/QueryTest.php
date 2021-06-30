<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\db\Container;
use suql\syntax\Query;
use test\suql\models\TableName;
use test\suql\models\TempTable;

final class QueryTest extends TestCase
{
    public function testQuery(): void
    {
        Container::create(require('config/db.php'));
        Query::create('connection', 'create database db_test')->exec();
        Container::add(require('config/db-test.php'));
        Query::create('db_test', 'create table table_name(field int)')->exec();
        Query::create('db_test', 'insert into table_name (field) values (1), (2), (3)')->exec();

        $this->testTempTable();

        $data = TableName::all()->fetchAll();
        $firstRow = TableName::all()->order(['field' => 'desc'])->fetchOne();
        $count = Query::create('db_test', 'delete from table_name')->exec();

        Query::create('db_test', 'drop table table_name')->exec();
        Query::create('db_test', 'drop database db_test')->exec();

        $this->assertEquals(3, $count);
        $this->assertEquals([
            ['field' => '1'],
            ['field' => '2'],
            ['field' => '3'],
        ], $data);
        $this->assertEquals([
            'field' => 3,
        ], $firstRow);
    }

    private function testTempTable()
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
}
