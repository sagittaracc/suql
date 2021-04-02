<?php declare(strict_types = 1);
use core\SuQLSpecialSymbols;
use PHPUnit\Framework\TestCase;
use sagittaracc\model\UserDb;
use sagittaracc\model\ProductDb;

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

  public function testSelectFromTwoDifferentDatabases(): void
  {
    $this->assertTrue(true);

    //
    // UserDb gets the data from the test database (see the UserDb model)
    //
    // $this->assertEquals(
    //   UserDb::db()->fetch(), [
    //     0 => [
    //       'id' => '1',
    //       'login' => 'admin',
    //       'password' => '123',
    //       'role' => '1',
    //       0 => '1',
    //       1 => 'admin',
    //       2 => '123',
    //       3 => '1',
    //     ]
    //   ]
    // );

    //
    // ProductDb gets the data from the store database (see the ProductDb model)
    //
    // $this->assertEquals(
    //   ProductDb::db()->fetch(), [
    //     0 => [
    //       'id' => '1',
    //       'name' => 'bottle',
    //       0 => '1',
    //       1 => 'bottle'
    //     ]
    //   ]
    // );
  }
}
