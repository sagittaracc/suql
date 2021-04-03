<?php declare(strict_types = 1);
use core\SuQLSpecialSymbols;
use PHPUnit\Framework\TestCase;
use app\model\User;

final class MySuQLExtTest extends TestCase
{
  public function testSuQLExtension(): void
  {
    $this->assertEquals(
      User::find()->max('id')->getRawSql(),
      'select max(users.id) from users'
    );

    $this->assertEquals(
      User::find()->filterLike('name', 'yuriy')->getRawSql(),
      "select users.name from users where users.name like '%yuriy%'"
    );

    $this->assertEquals(
      User::find()->filterLike('name', null)->getRawSql(),
      'select users.name from users'
    );
  }

  public function testSuQLExtensionArithmeticModifier(): void
  {
    $this->assertEquals(
      User::find()->field('id', [
        'div' => [2]
      ])->getRawSql(),
      'select users.id / 2 from users'
    );
  }
}
