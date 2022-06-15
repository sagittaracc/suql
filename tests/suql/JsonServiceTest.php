<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\db\Container;
use suql\syntax\Query;
use test\suql\models\Query26;
use test\suql\models\Query29;

final class JsonServiceTest extends TestCase
{
    public function setUp(): void
    {
        // Create a database
        Container::create(require('config/db-null.php'));
        Query::create('create database db_test')->setConnection('connection')->exec();
        Container::add(require('config/db.php'));
        Query::create('create table table_26(f1 int, f2 int, primary key (f1))')->setConnection('db_test')->exec();
        Query::create('insert into table_26 (f1, f2) values (1, 1), (2, 2), (3, 3)')->setConnection('db_test')->exec();
    }

    public function tearDown(): void
    {
        // Drop the database
        Query::create('drop database db_test')->setConnection('db_test')->exec();
    }

    public function testJsonService(): void
    {
        $actual = Query29::call('generateIntegers')->withParams([
            'apiKey' => '175082e4-b7bb-43cc-a018-df19794bcbb2',
            'n' => 3,
            'min' => 1,
            'max' => 3
        ])->join(Query26::class)->select(['f2'])->fetchOne();
        foreach ($actual as $number) {
            $this->assertLessThan(4, $number);
            $this->assertGreaterThan(0, $number);
        }
        $this->assertLessThan(4, count($actual));
    }
}
