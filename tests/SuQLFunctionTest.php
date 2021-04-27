<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;
use suql\core\SuQLPlaceholder;

final class SuQLFunctionTest extends TestCase
{
  public function testStoredProcedure(): void
  {
    $this->assertEquals(
        SuQLProcedure::find('setSomething')->params(1, 'string', null, true, false, new SuQLPlaceholder('id'))->getRawSql(),
        "call setSomething(1,'string',NULL,true,false,:id)"
    );

    $this->assertEquals(
        SuQLFunction::find('getSomething')->params(1, 'string', null, true, false, new SuQLPlaceholder('id'))->getRawSql(),
        "select getSomething(1,'string',NULL,true,false,:id)"
    );
  }
}
