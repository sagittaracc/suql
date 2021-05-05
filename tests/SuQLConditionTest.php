<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\core\SuQLCondition;
use suql\core\SuQLFieldName;

final class SuQLConditionTest extends TestCase
{
    private $field;

    protected function setUp(): void
    {
        $this->field = new SuQLFieldName('users', 'id');
    }

    protected function tearDown(): void
    {
        $this->field = null;
    }

    public function testSimpleCondition(): void
    {
        $expectedCondition = 'id % 2 = 0';
        $actualCondition = new SuQLCondition($this->field, '$ % 2 = 0');

        $this->assertEquals($expectedCondition, $actualCondition);
    }

    public function testConditionFormatFieldName(): void
    {
        $expectedCondition = 'users.id % 2 = 0';
        $actualCondition = new SuQLCondition($this->field, '$ % 2 = 0');
        $actualCondition->setFormat('%t.%n');

        $this->assertEquals($expectedCondition, $actualCondition);
    }
}
