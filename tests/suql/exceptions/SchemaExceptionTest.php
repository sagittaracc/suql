<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use test\suql\models\Query21;

final class SchemaExceptionTest extends TestCase
{
    public function testFailSchema(): void
    {
        $this->expectExceptionMessage('Schema failed. test\\suql\\schema\\UndefinedSchema not found!');
        $actual = Query21::all();
    }
}