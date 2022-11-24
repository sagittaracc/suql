<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\db\Container;
use suql\syntax\Query;

class DbTestCase extends TestCase
{
    protected $db = null;

    public function setUp(): void
    {
        $this->db = Query::create();

        Container::create(require('config/db-null.php'));
        
        $this->db->setConnection('connection');
        $this->db->query('create database db_test')->exec();

        Container::add(require('config/db.php'));

        $this->db->setConnection('db_test');
        $this->db->query('create table table_10(f1 int, f2 int)')->exec();
        $this->db->query('insert into table_10 (f1, f2) values (1, 1), (2, 2), (3, 3)')->exec();
        $this->db->query('insert into table_10 (f1, f2) values (?, ?), (?, ?), (?, ?)')->exec([4, 4, 5, 5, 6, 6]);
    }

    public function tearDown(): void
    {
        $this->db->query('drop table table_10')->exec();
        $this->db->query('drop database db_test')->exec();
    }
}
