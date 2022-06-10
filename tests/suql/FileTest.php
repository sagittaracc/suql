<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\db\Container;
use suql\syntax\Query;
use test\suql\models\Query28;

final class FileTest extends TestCase
{
    public function setUp(): void
    {
        // Create a database
        Container::create(require('config/db-null.php'));
        Query::create('create database db_test')->setConnection('connection')->exec();
        Container::add(require('config/db.php'));
    }

    public function tearDown(): void
    {
        // Drop the database
        Query::create('drop database db_test')->setConnection('db_test')->exec();
    }

    public function testFile(): void
    {
        $expected = [
            ['f1' => '1', 'f2' => '1'],
            ['f1' => '2', 'f2' => '2'],
            ['f1' => '3', 'f2' => '3'],
        ];
        $actual = Query28::find(['f1', 'f2'])->fetchAll();
        $this->assertSame($expected, $actual);
    }
}
