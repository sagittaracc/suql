<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\core\SuQLBetweenParam;
use suql\core\SuQLCondition;
use suql\core\SuQLFieldName;
use suql\core\SuQLInParam;
use suql\core\SuQLLikeParam;
use suql\core\SuQLPlaceholder;
use suql\core\SuQLSimpleParam;

final class SuQLConditionTest extends TestCase
{
    private $fieldUserId;
    private $fieldUserName;

    protected function setUp(): void
    {
        $this->fieldUserId = new SuQLFieldName('users', 'id');
        $this->fieldUserName = new SuQLFieldName('users', 'name');
    }

    protected function tearDown(): void
    {
        $this->fieldUserId = null;
        $this->fieldUserName = null;
    }

    public function testSimpleParamCondition(): void
    {
        $expectedCondition = 'id = :ph0_3ced11dfdbcf0d0ca4f89ad0cabc664b';
        $expectedParams = [':ph0_3ced11dfdbcf0d0ca4f89ad0cabc664b' => 1];

        $simpleParam = new SuQLSimpleParam($this->fieldUserId, [1]);
        $actualCondition = new SuQLCondition($simpleParam, '$ = ?');

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

        
        $betweenParam = new SuQLBetweenParam($this->fieldUserId, [1, 3]);
        $actualCondition = new SuQLCondition($betweenParam, '$ between ?');
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

        $inParam = new SuQLInParam($this->fieldUserId, [1, 2, 3]);
        $actualCondition = new SuQLCondition($inParam, '$ in ?');

        $this->assertEquals($expectedCondition, $actualCondition->getCondition());
        $this->assertEquals($expectedParams, $actualCondition->getParams());
    }

    public function testLikeParamCondition(): void
    {
        $expectedCondition = 'users.name like :ph0_c52e9ca1ce023b250556fab760727d9e';
        $expectedParams = [
            ':ph0_c52e9ca1ce023b250556fab760727d9e' => '%sagittaracc%',
        ];

        $likeParam = new SuQLLikeParam($this->fieldUserName, ['sagittaracc']);
        $actualCondition = new SuQLCondition($likeParam, '$ like ?', '%t.%n');

        $this->assertEquals($expectedCondition, $actualCondition->getCondition());
        $this->assertEquals($expectedParams, $actualCondition->getParams());
    }

    public function testPlaceholderParamCondition(): void
    {
        $expectedCondition = 'id = :id';

        $placeholderParam = new SuQLSimpleParam($this->fieldUserId, [new SuQLPlaceholder('id')]);
        $actualCondition = new SuQLCondition($placeholderParam, '$ = ?');

        $this->assertEquals($expectedCondition, $actualCondition->getCondition());
    }
}
