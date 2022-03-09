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
    private $fieldF1;
    private $fieldF2;

    protected function setUp(): void
    {
        $this->fieldF1 = new FieldName('table_1', ['f1' => 'af1']);
        $this->fieldF2 = new FieldName('table_1', ['f2' => 'af2']);
    }

    protected function tearDown(): void
    {
        $this->fieldF1 = null;
        $this->fieldF2 = null;
    }

    public function testSimpleParam(): void
    {
        $simpleParam = new SimpleParam($this->fieldF1, [1]);

        $this->assertEquals([1], $simpleParam->getParams());
        $this->assertEquals('pk_' . md5('table_1.f1:0'), $simpleParam->getParamKey());
        $this->assertEquals([
            ':ph0_' . md5('table_1.f1:1') => 1,
        ], $simpleParam->getParamList());
        $this->assertTrue($simpleParam->isValuable());
    }

    public function testBetweenParam(): void
    {
        $betweenParam = new BetweenParam($this->fieldF1, [1, 3]);

        $this->assertEquals([1, 3], $betweenParam->getParams());
        $this->assertEquals('pk_' . md5('table_1.f1:0'), $betweenParam->getParamKey());
        $this->assertEquals([
            ':ph0_' . md5('table_1.f1:1') => 1,
            ':ph1_' . md5('table_1.f1:3') => 3,
        ], $betweenParam->getParamList());
        $this->assertTrue($betweenParam->isValuable());
    }

    public function testInParam(): void
    {
        $inParam = new InParam($this->fieldF1, [1, 2, 3]);

        $this->assertEquals([1, 2, 3], $inParam->getParams());
        $this->assertEquals('pk_' . md5('table_1.f1:0'), $inParam->getParamKey());
        $this->assertEquals([
            ':ph0_' . md5('table_1.f1:1') => 1,
            ':ph1_' . md5('table_1.f1:2') => 2,
            ':ph2_' . md5('table_1.f1:3') => 3,
        ], $inParam->getParamList());
        $this->assertTrue($inParam->isValuable());
    }

    public function testLikeParam(): void
    {
        $likeParam = new LikeParam($this->fieldF2, ['Yuriy Arutyunyan']);

        $this->assertEquals(['Yuriy Arutyunyan'], $likeParam->getParams());
        $this->assertEquals('pk_' . md5('table_1.f2:0'), $likeParam->getParamKey());
        $this->assertEquals([
            ':ph0_' . md5('table_1.f2:Yuriy Arutyunyan') => '%Yuriy Arutyunyan%',
        ], $likeParam->getParamList());
        $this->assertTrue($likeParam->isValuable());
    }

    public function testNotValuableParam(): void
    {
        $simpleParam = new SimpleParam($this->fieldF1, [null]);
        $betweenParam = new BetweenParam($this->fieldF1, [1, null]);
        $inParam = new InParam($this->fieldF1, [1, null, null]);
        $likeParam = new LikeParam($this->fieldF2, [null]);

        $this->assertFalse($simpleParam->isValuable());
        $this->assertFalse($betweenParam->isValuable());
        $this->assertFalse($inParam->isValuable());
        $this->assertFalse($likeParam->isValuable());
    }

    public function testPlaceholderParam(): void
    {
        $placeholderParam = new SimpleParam($this->fieldF1, [new Placeholder('f1')]);

        $this->assertArrayHasKey(':f1', $placeholderParam->getParamList());
        $this->assertCount(1, $placeholderParam->getParamList());
        $this->assertInstanceOf(Placeholder::class, $placeholderParam->getParamList()[':f1']);
    }
}