<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\builder\SqliteBuilder;
use suql\db\Container;
use test\suql\models\Query18;

final class SqliteTest extends TestCase
{
    public function testRealSqliteDb(): void
    {
        Container::create(require('config/db-sqlite.php'));
        $tmp = Query18::all()
            ->select([
                'f1' => 'af1',
                'f2' => 'af2',
            ])
            ->fetchAll();
        $this->assertEquals([
            ['af1' => '1', 'af2' => '1'],
            ['af1' => '2', 'af2' => '2'],
            ['af1' => '3', 'af2' => '3'],
        ], $tmp);
    }
}
