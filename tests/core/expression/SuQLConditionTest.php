<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\core\BetweenParam;
use suql\core\Condition;
use suql\core\FieldName;
use suql\core\InParam;
use suql\core\LikeParam;
use suql\core\Placeholder;
use suql\core\SimpleParam;

final class SuQLConditionTest extends TestCase
{
    private $fieldF1;
    private $fieldF2;

    protected function setUp(): void
    {
        $this->fieldF1 = new FieldName('table_1', 'f1');
        $this->fieldF2 = new FieldName('table_1', 'f2');
    }

    protected function tearDown(): void
    {
        $this->fieldF1 = null;
        $this->fieldF2 = null;
    }

    public function testSimpleParamCondition(): void
    {
        $expectedCondition = 'f1 = :ph0_8008c0fb0d9e45eeab00d02d4dc6bf1b';
        $expectedParams = [':ph0_8008c0fb0d9e45eeab00d02d4dc6bf1b' => 1];

        $simpleParam = new SimpleParam($this->fieldF1, [1]);
        $actualCondition = new Condition($simpleParam, '$ = ?');

        $this->assertEquals($expectedCondition, $actualCondition->getCondition());
        $this->assertEquals($expectedParams, $actualCondition->getParams());
    }

    public function testBetweenParamCondition(): void
    {
        $expectedCondition = 'table_1.f1 between :ph0_8008c0fb0d9e45eeab00d02d4dc6bf1b and :ph1_687821896f80eb70591ac4f812a45fe3';
        $expectedParams = [
            ':ph0_8008c0fb0d9e45eeab00d02d4dc6bf1b' => 1,
            ':ph1_687821896f80eb70591ac4f812a45fe3' => 3,
        ];

        
        $betweenParam = new BetweenParam($this->fieldF1, [1, 3]);
        $actualCondition = new Condition($betweenParam, '$ between ?');
        $actualCondition->setFormat('%t.%n');

        $this->assertEquals($expectedCondition, $actualCondition->getCondition());
        $this->assertEquals($expectedParams, $actualCondition->getParams());
    }

    public function testInParamCondition(): void
    {
        $expectedCondition = 'f1 in (:ph0_8008c0fb0d9e45eeab00d02d4dc6bf1b,:ph1_f8e904b767520bcfd4819447bddeafc4,:ph2_687821896f80eb70591ac4f812a45fe3)';
        $expectedParams = [
            ':ph0_8008c0fb0d9e45eeab00d02d4dc6bf1b' => 1,
            ':ph1_f8e904b767520bcfd4819447bddeafc4' => 2,
            ':ph2_687821896f80eb70591ac4f812a45fe3' => 3,
        ];

        $inParam = new InParam($this->fieldF1, [1, 2, 3]);
        $actualCondition = new Condition($inParam, '$ in ?');

        $this->assertEquals($expectedCondition, $actualCondition->getCondition());
        $this->assertEquals($expectedParams, $actualCondition->getParams());
    }

    public function testLikeParamCondition(): void
    {
        $expectedCondition = 'table_1.f2 like :ph0_2ce8d160537737958bcef91a22b01044';
        $expectedParams = [
            ':ph0_2ce8d160537737958bcef91a22b01044' => '%sagittaracc%',
        ];

        $likeParam = new LikeParam($this->fieldF2, ['sagittaracc']);
        $actualCondition = new Condition($likeParam, '$ like ?', '%t.%n');

        $this->assertEquals($expectedCondition, $actualCondition->getCondition());
        $this->assertEquals($expectedParams, $actualCondition->getParams());
    }

    public function testPlaceholderParamCondition(): void
    {
        $expectedCondition = 'f1 = :f1';

        $placeholderParam = new SimpleParam($this->fieldF1, [new Placeholder('f1')]);
        $actualCondition = new Condition($placeholderParam, '$ = ?');

        $this->assertEquals($expectedCondition, $actualCondition->getCondition());
    }
}
