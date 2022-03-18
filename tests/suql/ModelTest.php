<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\syntax\Model;

final class ModelTest extends TestCase
{
    public function testModel(): void
    {
        $model = Model::create('table_1')
            ->column('t1')
                ->setType('int')
                ->setLength(11)
                ->setDefault(null)
            ->column('t2')
                ->setType('varchar')
                ->setLength(255)
                ->setDefault(0);
        
        $this->assertEquals(2, count($model->getColumns()));
        $this->assertEquals('t2', $model->getCurrentColumn());

        $this->assertEquals('int', $model->getTypeByColumnName('t1'));
        $this->assertEquals(11, $model->getLengthByColumnName('t1'));
        $this->assertEquals(null, $model->getDefaultByColumnName('t1'));

        $this->assertEquals('varchar', $model->getTypeByColumnName('t2'));
        $this->assertEquals(255, $model->getLengthByColumnName('t2'));
        $this->assertEquals(0, $model->getDefaultByColumnName('t2'));
        
    }
}
