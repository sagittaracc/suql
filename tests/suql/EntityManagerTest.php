<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\builder\MySQLBuilder;
use suql\manager\EntityManager;
use test\suql\models\Query1;
use test\suql\schema\AppScheme;

final class EntityManagerTest extends TestCase
{
    private $orm;

    public function setUp(): void
    {
        $this->orm = new EntityManager();
        $this->orm->setScheme(AppScheme::class);
        $this->orm->setBuilder(MySQLBuilder::class);
    }

    public function tearDown(): void
    {
        $this->orm = null;
    }
    /**
     * Example:
     * 
     * select * from table
     * 
     */
    public function testSelect(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q4.php'));

        $query1 = $this->orm->getRepository(Query1::class);

        $actual = $query1->select([
            'f1' => 'af1',
            'f2' => 'af2',
        ])->getRawSql();
        $this->assertEquals($expected, $actual);
    }
}
