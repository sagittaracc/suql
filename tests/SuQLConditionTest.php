<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\core\SuQLBetweenParam;
use suql\core\SuQLCondition;
use suql\core\SuQLFieldName;
use suql\core\SuQLSimpleParam;

final class SuQLConditionTest extends TestCase
{
    private $simple_param;
    private $between_param;

    protected function setUp(): void
    {
        $this->simple_param = new SuQLSimpleParam(
            new SuQLFieldName('users', 'id'),
            [0]
        );

        $this->between_param = new SuQLBetweenParam(
            new SuQLFieldName('users', 'id'),
            [1, 3]
        );
    }

    protected function tearDown(): void
    {
        $this->simple_param = null;
        $this->between_param = null;
    }

    public function testSimpleParamCondition(): void
    {
        $expectedCondition = 'id % 2 = :ph0_fc02896e3034a4ed53259916e2e2d82d';
        $expectedParams = [':ph0_fc02896e3034a4ed53259916e2e2d82d' => 0];

        $actualCondition = new SuQLCondition($this->simple_param, '$ % 2 = ?');

        $this->assertEquals($expectedCondition, $actualCondition->getCondition());
        $this->assertEquals($expectedParams, $actualCondition->getParams());
    }

    public function testBetweenParamCondition(): void
    {
        $expectedCondition = 'users.id between :ph0_fc02896e3034a4ed53259916e2e2d82d and :ph1_fc02896e3034a4ed53259916e2e2d82d';
        $expectedParams = [
            ':ph0_fc02896e3034a4ed53259916e2e2d82d' => 1,
            ':ph1_fc02896e3034a4ed53259916e2e2d82d' => 3,
        ];

        $actualCondition = new SuQLCondition($this->between_param, '$ between ?');
        $actualCondition->setFormat('%t.%n');

        $this->assertEquals($expectedCondition, $actualCondition->getCondition());
        $this->assertEquals($expectedParams, $actualCondition->getParams());
    }
}
