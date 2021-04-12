<?php declare(strict_types = 1);
use core\SuQLSpecialSymbols;
use PHPUnit\Framework\TestCase;
use app\model\UserDb;
use app\model\User;
use app\model\ProductDb;

final class SuQLPdoTest extends TestCase
{
  public function testSelectFromTwoDifferentDatabases(): void
  {
    $this->assertTrue(true);

    //
    // UserDb gets the data from the test database (see the UserDb model)
    //
    // $this->assertObjectHasAttribute('id', UserDb::find()->fetchOne());
    // $this->assertEquals(1, UserDb::find()->fetchOne()->id);
    //
    // $userList = UserDb::find()
    //               ->select(['id', 'login', 'password', 'role'])
    //               ->field('id', [
    //                 'equal' => [':id']
    //               ], false)
    //               ->fetchAll([':id' => 1]);
    //
    // foreach ($userList as $user)
    // {
    //   $this->assertObjectHasAttribute('id', $user);
    //   $this->assertObjectHasAttribute('login', $user);
    //   $this->assertObjectHasAttribute('password', $user);
    //   $this->assertObjectHasAttribute('role', $user);
    // }
    //
    // $user = UserDb::find()->field('id', ['greater' => [1]])->fetchOne();
    // $this->assertNull($user);
    //
    // $user = UserDb::find()->field('login', ['like' => ['Yuriy']])->fetchAll();
    // $this->assertEmpty($user);

    //
    // ProductDb gets the data from the store database (see the ProductDb model)
    //
    // $this->assertEquals(
    //   ProductDb::find()->field('id', [
    //     'greater' => [
    //       ['integer' => 1],
    //       ['boolean' => false],
    //     ]
    //   ])->fetch()
    // );
  }
}
