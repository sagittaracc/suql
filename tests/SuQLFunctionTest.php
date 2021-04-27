<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;

final class SuQLFunctionTest extends TestCase
{
  public function testStoredProcedure(): void
  {
    $this->assertEquals(
        SuQLProcedure::find('setSomething')->params(1, 2, 3)->getRawSql(),
        // 'call setSomething(1,2,3)'
        ''
    );

    $this->assertEquals(
        SuQLFunction::find('getSomething')->params(1, 2, 3)->getRawSql(),
        // 'select getSomething(1,2,3)'
        ''
    );
  }
}
