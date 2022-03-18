<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\StringHelper;
use suql\builder\MySQLBuilder;
use suql\syntax\Model;

final class ModelTest extends TestCase
{
    private $model;

    public function setUp(): void
    {
        $this->model = Model::create('table_1')
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
        $this->assertEquals('table_1', $this->model->getName());
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
        $builder = new MySQLBuilder();

        $expected = StringHelper::trimSql(require('queries/mysql/q29.php'));
        $actual = $builder->buildModel($this->model);
        $this->assertEquals($expected, $actual);
    }
}
