<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\builder\MySQLBuilder;
use test\suql\models\Query1;

final class ModelTest extends TestCase
{
    private $model;

    public function setUp(): void
    {
        $this->model = Query1::all()
            ->column('t1')
                ->setType('int')
                ->setLength(11)
                ->setDefault(null)
            ->column('t2')
                ->setType('varchar')
                ->setLength(255)
                ->setDefault(0);
    }

    public function tearDown(): void
    {
        $this->model = null;
    }

    public function testModel(): void
    {
        $this->assertEquals('table_1', $this->model->table());
        $this->assertEquals(2, count($this->model->getColumns()));
        $this->assertEquals('t2', $this->model->getCurrentColumn());

        $this->assertEquals('int', $this->model->getTypeByColumnName('t1'));
        $this->assertEquals(11, $this->model->getLengthByColumnName('t1'));
        $this->assertEquals(null, $this->model->getDefaultByColumnName('t1'));

        $this->assertEquals('varchar', $this->model->getTypeByColumnName('t2'));
        $this->assertEquals(255, $this->model->getLengthByColumnName('t2'));
        $this->assertEquals(0, $this->model->getDefaultByColumnName('t2'));
    }

    public function testBuildModel(): void
    {
        $expected = StringHelper::trimSql(require('queries/mysql/q29.php'));
        $actual = $this->model->getBuilder()->buildModel($this->model);
        $this->assertEquals($expected, $actual);
    }
}
