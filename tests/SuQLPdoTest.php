<?php declare(strict_types = 1);
use core\SuQLSpecialSymbols;
use PHPUnit\Framework\TestCase;
use sagittaracc\model\UserDb;

final class SuQLPdoTest extends TestCase
{
  public function testSelectDb(): void
  {
    $this->assertTrue(true);

    /**
     * Example of fetching from database
     * 
     * $this->assertEquals(
     *   UserDb::db()->fetch(),
     *    [
     *      // Some data ...
     *    ]
     *  );
     */
  }
}
