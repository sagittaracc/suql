<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\core\SuQLBetweenParam;
use suql\core\SuQLCondition;
use suql\core\SuQLFieldName;
use suql\core\SuQLInParam;
use suql\core\SuQLLikeParam;
use suql\core\SuQLSimpleParam;

final class SuQLConditionTest extends TestCase
{
    private $fieldUserId;
    private $fieldUserName;

    private $simpleParam;
    private $betweenParam;
    private $inParam;
    private $likeParam;

    protected function setUp(): void
    {
        $this->fieldUserId = new SuQLFieldName('users', 'id');
        $this->fieldUserName = new SuQLFieldName('users', 'name');

        $this->simpleParam = new SuQLSimpleParam($this->fieldUserId, [1]);
        $this->betweenParam = new SuQLBetweenParam($this->fieldUserId, [1, 3]);
        $this->inParam = new SuQLInParam($this->fieldUserId, [1, 2, 3]);
        $this->likeParam = new SuQLLikeParam($this->fieldUserName, ['sagittaracc']);
    }

    protected function tearDown(): void
    {
        $this->fieldUserId = null;
        $this->fieldUserName = null;

        $this->simpleParam = null;
        $this->betweenParam = null;
        $this->inParam = null;
        $this->likeParam = null;
    }

    public function testSimpleParamCondition(): void
    {
        $expectedCondition = 'id = :ph0_fc02896e3034a4ed53259916e2e2d82d';
        $expectedParams = [':ph0_fc02896e3034a4ed53259916e2e2d82d' => 1];

        $actualCondition = new SuQLCondition($this->simpleParam, '$ = ?');

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

        $actualCondition = new SuQLCondition($this->betweenParam, '$ between ?');
        $actualCondition->setFormat('%t.%n');

        $this->assertEquals($expectedCondition, $actualCondition->getCondition());
        $this->assertEquals($expectedParams, $actualCondition->getParams());
    }

    public function testInParamCondition(): void
    {
        $expectedCondition = 'id in (:ph0_fc02896e3034a4ed53259916e2e2d82d,:ph1_fc02896e3034a4ed53259916e2e2d82d,:ph2_fc02896e3034a4ed53259916e2e2d82d)';
        $expectedParams = [
            ':ph0_fc02896e3034a4ed53259916e2e2d82d' => 1,
            ':ph1_fc02896e3034a4ed53259916e2e2d82d' => 2,
            ':ph2_fc02896e3034a4ed53259916e2e2d82d' => 3,
        ];

        $actualCondition = new SuQLCondition($this->inParam, '$ in ?');

        $this->assertEquals($expectedCondition, $actualCondition->getCondition());
        $this->assertEquals($expectedParams, $actualCondition->getParams());
    }

    public function testLikeParamCondition(): void
    {
        $expectedCondition = 'users.name like :ph0_12cb8fae9701df6e8e8b1b972362a7ff';
        $expectedParams = [
            ':ph0_12cb8fae9701df6e8e8b1b972362a7ff' => '%sagittaracc%',
        ];

        $actualCondition = new SuQLCondition($this->likeParam, '$ like ?', '%t.%n');

        $this->assertEquals($expectedCondition, $actualCondition->getCondition());
        $this->assertEquals($expectedParams, $actualCondition->getParams());
    }
}
