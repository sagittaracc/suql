<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use suql\core\BetweenParam;
use suql\core\FieldName;
use suql\core\InParam;
use suql\core\LikeParam;
use suql\core\Placeholder;
use suql\core\SimpleParam;

class SuQLParamTest extends TestCase
{
    private $fieldUserId;
    private $fieldUserName;

    protected function setUp(): void
    {
        $this->fieldUserId = new FieldName('users', ['id' => 'uid']);
        $this->fieldUserName = new FieldName('users', ['name' => 'uname']);
    }

    protected function tearDown(): void
    {
        $this->fieldUserId = null;
        $this->fieldUserName = null;
    }

    public function testSimpleParam(): void
    {
        $simpleParam = new SimpleParam($this->fieldUserId, [1]);

        $this->assertEquals([1], $simpleParam->getParams());
        $this->assertEquals('pk_' . md5('users.id:0'), $simpleParam->getParamKey());
        $this->assertEquals([
            ':ph0_' . md5('users.id:1') => 1,
        ], $simpleParam->getParamList());
        $this->assertTrue($simpleParam->isValuable());
    }

    public function testBetweenParam(): void
    {
        $betweenParam = new BetweenParam($this->fieldUserId, [1, 3]);

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
        $inParam = new InParam($this->fieldUserId, [1, 2, 3]);

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
        $likeParam = new LikeParam($this->fieldUserName, ['sagittaracc']);

        $this->assertEquals(['sagittaracc'], $likeParam->getParams());
        $this->assertEquals('pk_' . md5('users.name:0'), $likeParam->getParamKey());
        $this->assertEquals([
            ':ph0_' . md5('users.name:sagittaracc') => '%sagittaracc%',
        ], $likeParam->getParamList());
        $this->assertTrue($likeParam->isValuable());
    }

    public function testNotValuableParam(): void
    {
        $simpleParam = new SimpleParam($this->fieldUserId, [null]);
        $betweenParam = new BetweenParam($this->fieldUserId, [1, null]);
        $inParam = new InParam($this->fieldUserId, [1, null, null]);
        $likeParam = new LikeParam($this->fieldUserName, [null]);

        $this->assertFalse($simpleParam->isValuable());
        $this->assertFalse($betweenParam->isValuable());
        $this->assertFalse($inParam->isValuable());
        $this->assertFalse($likeParam->isValuable());
    }

    public function testPlaceholderParam(): void
    {
        $placeholderParam = new SimpleParam($this->fieldUserId, [new Placeholder('id')]);

        $this->assertArrayHasKey(':id', $placeholderParam->getParamList());
        $this->assertCount(1, $placeholderParam->getParamList());
        $this->assertInstanceOf(Placeholder::class, $placeholderParam->getParamList()[':id']);
    }
}