<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;
use suql\core\SuQLPlaceholder;

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

    $this->assertEquals(
      SuQLProcedure::find('getSomething')->params(new SuQLPlaceholder('id'), 'string', null)->getRawSql(),
      "call getSomething(:id,'string',NULL)"
    );

    $this->assertEquals(
      SuQLFunction::find('getSomething')->params(new SuQLPlaceholder('id'), 'string', null)->getRawSql(),
      "select getSomething(:id,'string',NULL)"
    );
  }
}
