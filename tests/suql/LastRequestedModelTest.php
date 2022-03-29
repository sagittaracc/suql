<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use test\suql\models\Query1;

final class LastRequestedModelTest extends TestCase
{
    public function testMainModel(): void
    {
        $query = Query1::all();
        $this->assertEquals('test\suql\models\Query1', $query->getLastRequestedModel());
    }

    public function testLastRequestedModel(): void
    {
        $query = Query1::all()
            ->getQuery2();

        $this->assertEquals('test\suql\models\Query2', $query->getLastRequestedModel());
    }
}
