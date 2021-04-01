<?php declare(strict_types = 1);
use core\SuQLSpecialSymbols;
use PHPUnit\Framework\TestCase;
use sagittaracc\model\User;
use sagittaracc\model\UserView;
use sagittaracc\model\GroupView;

final class SuQLTest extends TestCase
{
  public function testUser(): void
  {
    $this->assertEquals(
      User::find()
              ->select(['id', 'name'])
              ->getRawSql(),
      'select users.id, users.name from users'
    );
  }

  public function testUserView(): void
  {
    $this->assertEquals(
      UserView::find()->getRawSql(),
      'select * from (select users.id, users.name from users) user'
    );
  }

  public function testGroupView(): void
  {
    $this->assertEquals(
      GroupView::find()->getRawSql(),
      'select * from (select * from (select users.id, users.name from users) user) userView'
    );
  }
}
