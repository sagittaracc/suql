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
        $expectedCondition = 'id = :ph0_3ced11dfdbcf0d0ca4f89ad0cabc664b';
        $expectedParams = [':ph0_3ced11dfdbcf0d0ca4f89ad0cabc664b' => 1];

        $actualCondition = new SuQLCondition($this->simpleParam, '$ = ?');

        $this->assertEquals($expectedCondition, $actualCondition->getCondition());
        $this->assertEquals($expectedParams, $actualCondition->getParams());
    }

    public function testBetweenParamCondition(): void
    {
        $expectedCondition = 'users.id between :ph0_3ced11dfdbcf0d0ca4f89ad0cabc664b and :ph1_b90e7265948fc8b12c62f17f6f2c5363';
        $expectedParams = [
            ':ph0_3ced11dfdbcf0d0ca4f89ad0cabc664b' => 1,
            ':ph1_b90e7265948fc8b12c62f17f6f2c5363' => 3,
        ];

        $actualCondition = new SuQLCondition($this->betweenParam, '$ between ?');
        $actualCondition->setFormat('%t.%n');

        $this->assertEquals($expectedCondition, $actualCondition->getCondition());
        $this->assertEquals($expectedParams, $actualCondition->getParams());
    }

    public function testInParamCondition(): void
    {
        $expectedCondition = 'id in (:ph0_3ced11dfdbcf0d0ca4f89ad0cabc664b,:ph1_3aeb5fe8e84508eecd31e480918704a7,:ph2_b90e7265948fc8b12c62f17f6f2c5363)';
        $expectedParams = [
            ':ph0_3ced11dfdbcf0d0ca4f89ad0cabc664b' => 1,
            ':ph1_3aeb5fe8e84508eecd31e480918704a7' => 2,
            ':ph2_b90e7265948fc8b12c62f17f6f2c5363' => 3,
        ];

        $actualCondition = new SuQLCondition($this->inParam, '$ in ?');

        $this->assertEquals($expectedCondition, $actualCondition->getCondition());
        $this->assertEquals($expectedParams, $actualCondition->getParams());
    }

    public function testLikeParamCondition(): void
    {
        $expectedCondition = 'users.name like :ph0_c52e9ca1ce023b250556fab760727d9e';
        $expectedParams = [
            ':ph0_c52e9ca1ce023b250556fab760727d9e' => '%sagittaracc%',
        ];

        $actualCondition = new SuQLCondition($this->likeParam, '$ like ?', '%t.%n');

        $this->assertEquals($expectedCondition, $actualCondition->getCondition());
        $this->assertEquals($expectedParams, $actualCondition->getParams());
    }
}
