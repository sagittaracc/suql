<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\core\SuQLBetweenParam;
use suql\core\SuQLFieldName;
use suql\core\SuQLInParam;
use suql\core\SuQLLikeParam;
use suql\core\SuQLSimpleParam;

class SuQLParamTest extends TestCase
{
    private $fieldUserId;
    private $fieldUserName;

    protected function setUp(): void
    {
        $this->fieldUserId = new SuQLFieldName('users', ['id' => 'uid']);
        $this->fieldUserName = new SuQLFieldName('users', ['name' => 'uname']);
    }

    protected function tearDown(): void
    {
        $this->fieldUserId = null;
        $this->fieldUserName = null;
    }

    public function testSimpleParam(): void
    {
        $simpleParam = new SuQLSimpleParam($this->fieldUserId, [1]);

        $this->assertEquals([1], $simpleParam->getParams());
        $this->assertEquals('pk_' . md5('users.id:0'), $simpleParam->getParamKey());
        $this->assertEquals([
            ':ph0_' . md5('users.id:1') => 1,
        ], $simpleParam->getParamList());
        $this->assertTrue($simpleParam->isValuable());
    }

    public function testBetweenParam(): void
    {
        $betweenParam = new SuQLBetweenParam($this->fieldUserId, [1, 3]);

        $this->assertEquals([1, 3], $betweenParam->getParams());
        $this->assertEquals('pk_' . md5('users.id:0'), $betweenParam->getParamKey());
        $this->assertEquals([
            ':ph0_' . md5('users.id:1') => 1,
            ':ph1_' . md5('users.id:3') => 3,
        ], $betweenParam->getParamList());
        $this->assertTrue($betweenParam->isValuable());
    }

    public function testInParam(): void
    {
        $inParam = new SuQLInParam($this->fieldUserId, [1, 2, 3]);

        $this->assertEquals([1, 2, 3], $inParam->getParams());
        $this->assertEquals('pk_' . md5('users.id:0'), $inParam->getParamKey());
        $this->assertEquals([
            ':ph0_' . md5('users.id:1') => 1,
            ':ph1_' . md5('users.id:2') => 2,
            ':ph2_' . md5('users.id:3') => 3,
        ], $inParam->getParamList());
        $this->assertTrue($inParam->isValuable());
    }

    public function testLikeParam(): void
    {
        $likeParam = new SuQLLikeParam($this->fieldUserName, ['sagittaracc']);

        $this->assertEquals(['sagittaracc'], $likeParam->getParams());
        $this->assertEquals('pk_' . md5('users.name:0'), $likeParam->getParamKey());
        $this->assertEquals([
            ':ph0_' . md5('users.name:sagittaracc') => '%sagittaracc%',
        ], $likeParam->getParamList());
        $this->assertTrue($likeParam->isValuable());
    }

    public function testNotValuableParam(): void
    {
        $simpleParam = new SuQLSimpleParam($this->fieldUserId, [null]);
        $betweenParam = new SuQLBetweenParam($this->fieldUserId, [1, null]);
        $inParam = new SuQLInParam($this->fieldUserId, [1, null, null]);
        $likeParam = new SuQLLikeParam($this->fieldUserName, [null]);

        $this->assertFalse($simpleParam->isValuable());
        $this->assertFalse($betweenParam->isValuable());
        $this->assertFalse($inParam->isValuable());
        $this->assertFalse($likeParam->isValuable());
    }
}