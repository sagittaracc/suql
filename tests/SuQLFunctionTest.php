<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;

final class SuQLFunctionTest extends TestCase
{
  public function testStoredProcedure(): void
  {
    $this->assertEquals(
        SuQLProcedure::find('setSomething')->params(1, 'string', null)->getRawSql(),
        "call setSomething(1,'string',NULL)"
    );

    $this->assertEquals(
        SuQLFunction::find('getSomething')->params(1, 'string', null)->getRawSql(),
        "select getSomething(1,'string',NULL)"
    );
  }
}
