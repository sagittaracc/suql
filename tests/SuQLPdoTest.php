<?php declare(strict_types = 1);
use core\SuQLSpecialSymbols;
use PHPUnit\Framework\TestCase;
use app\model\UserDb;
use app\model\ProductDb;

final class SuQLPdoTest extends TestCase
{
  public function testSelectFromTwoDifferentDatabases(): void
  {
    $this->assertTrue(true);

    //
    // UserDb gets the data from the test database (see the UserDb model)
    //
    // $this->assertEquals(
    //   UserDb::find()->fetch(), [
    //     Some data
    //   ]
    // );

    //
    // ProductDb gets the data from the store database (see the ProductDb model)
    //
    // $this->assertEquals(
    //   ProductDb::find()->fetch(), [
    //     Some data
    //   ]
    // );
  }
}
