<?php declare(strict_types = 1);
use core\SuQLSpecialSymbols;
use PHPUnit\Framework\TestCase;
use sagittaracc\model\User;
use sagittaracc\model\UserView;
use sagittaracc\model\GroupView;

final class SuQLTest extends TestCase
{
  // public function testUser(): void
  // {
  //   var_dump(User::find());
  //   $this->assertEquals(
  //     User::find()->getSQL('all'),
  //     'select users.* from users'
  //   );
  // }

  public function testUserView(): void
  {
    $this->assertEquals(
      UserView::find()->getSQL(['userView']),
      'select * from (select users.* from users) userView'
    );
  }

  // public function testGroupView(): void
  // {
  //   $this->assertEquals(
  //     GroupView::find()->getSQL(['groupView']),
  //     'select * from (select userView.* from userView) groupView'
  //   );
  // }
}
