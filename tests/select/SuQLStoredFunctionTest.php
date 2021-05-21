<?php

declare(strict_types=1);

final class SuQLStoredFunctionTest extends SuQLMock
{
    /**
     * SELECT <function>(<parameters>)
     */
    public function testStoredFunction(): void
    {
        $sql = "select some_func(1,false,'Yuriy',NULL)";

        $this->osuql->addFunction('stored_function', 'some_func');
        $this->osuql->getQuery('stored_function')->addParams([1, false, 'Yuriy', null]);
        $suql = $this->osuql->getSQL(['stored_function']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['stored_function']));
    }
    /**
     * CALL <procedure>(<parameters>)
     */
    public function testStoredProcedure(): void
    {
        $sql = "call some_proc(1,false,'Yuriy',NULL)";

        $this->osuql->addProcedure('stored_procedure', 'some_proc');
        $this->osuql->getQuery('stored_procedure')->addParams([1, false, 'Yuriy', null]);
        $suql = $this->osuql->getSQL(['stored_procedure']);

        $this->assertEquals($sql, $suql);
        $this->assertNull($this->osuql->getSQL(['stored_procedure']));
    }
}