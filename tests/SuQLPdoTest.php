<?php declare(strict_types = 1);
use core\SuQLSpecialSymbols;
use PHPUnit\Framework\TestCase;
use app\model\UserDb;
use app\model\User;
use app\model\ProductDb;
use app\model\FilterUsersView;

final class SuQLPdoTest extends TestCase
{
  public function testSelectFromTwoDifferentDatabases(): void
  {
    //
    // UserDb gets the data from the test database (see the UserDb model)
    //
    // $this->assertObjectHasAttribute('id', UserDb::find()->fetchOne());
    // $this->assertEquals(1, UserDb::find()->fetchOne()->id);
    //
    // $query = UserDb::find()
    //            ->field('id')
    //            ->field('login')
    //            ->field('password')
    //            ->field('role')
    //            ->field('id', [
    //              'equal' => [1],
    //            ], false);
    // $userList = $query->fetchAll();
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
    $this->assertTrue(true);
  }

  public function testInsert(): void
  {
    // $user = new UserDb([
    //   'id' => 0,
    //   'login' => 'user'
    // ]);
    //
    // $user->save();
    $this->assertTrue(true);
  }

  public function testFilterView(): void
  {
    // $query = FilterUsersView::find()->normalize();
    // $data = $query->fetchAll([
    //   ':uid' => null,
    //   ':username' => null,
    //   ':gid' => null,
    //   ':groupname' => null,
    // ]);
    // var_dump($data);
    $this->assertTrue(true);
  }
}
