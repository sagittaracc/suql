<?php declare(strict_types = 1);
use core\SuQLSpecialSymbols;
use PHPUnit\Framework\TestCase;
use sagittaracc\model\User;
use sagittaracc\model\UserView;

final class SuQLTest extends TestCase
{
  public function testUser(): void
  {
    $this->assertEquals(User::find()->getSQL('all'), 'select users.id from users');
  }

  public function testUserView(): void
  {
    $this->assertEquals(UserView::find()->getSQL(['main']), 'select * from (select users.id from users) subquery');
  }
}
