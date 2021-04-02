<?php declare(strict_types = 1);
use core\SuQLSpecialSymbols;
use PHPUnit\Framework\TestCase;
use sagittaracc\model\User;
use sagittaracc\model\UserGroup;
use sagittaracc\model\Group;
use sagittaracc\model\UserGroupView;

final class SuQLTest extends TestCase
{
  public function testSelect(): void
  {
    // Select all the fields
    $this->assertEquals(
      User::find()->getRawSql(),
      'select * from users'
    );

    // Select some specific fields
    $this->assertEquals(
      User::find()->select(['id', 'name'])->getRawSql(),
      'select users.id, users.name from users'
    );

    // Set aliases for the fields
    $this->assertEquals(
      User::find()->select(['id' => 'uid', 'name' => 'uname'])->getRawSql(),
      'select users.id as uid, users.name as uname from users'
    );
  }

  public function testJoin(): void
  {
    // Join all tables
    $this->assertEquals(
      User::find()->join(UserGroup::class)
                  ->join(Group::class)
                  ->getRawSql(),
      'select * from users inner join user_group on users.id = user_group.user_id inner join groups on user_group.group_id = groups.id'
    );
  }

  public function testModifiers(): void
  {
    $this->assertEquals(
      User::find()->join(UserGroup::class)
                  ->join(Group::class)
                    ->field('name')
                    ->field(['name' => 'count'], [
                      'group' => [],
                      'count' => [],
                    ])
                  ->getRawSql(),
      'select groups.name, count(groups.name) as count from users inner join user_group on users.id = user_group.user_id inner join groups on user_group.group_id = groups.id group by groups.name'
    );
  }

  public function testView(): void
  {
    $this->assertEquals(
      UserGroupView::find()->getRawSql(),
      'select * from (select * from users inner join user_group on users.id = user_group.user_id inner join groups on user_group.group_id = groups.id) user'
    );
  }

  public function testWhere(): void
  {
    $this->assertEquals(
      User::find()
              ->field('id', [
                'where' => ['$ mod 2 = 0']
              ])
              ->field(['name' => 'userName'])
              ->getRawSql(),
      'select users.id, users.name as userName from users where users.id mod 2 = 0'
    );

    $this->assertEquals(
      User::find()
              ->field('name', [
                'like' => ['yuriy']
              ])
              ->field('id', [
                'greater' => [10],
              ])
              ->getRawSql(),
      "select users.name, users.id from users where users.name like '%yuriy%' and users.id > 10"
    );

    // Where by filters
    $this->assertEquals(
      User::find()
              ->field('name', [
                'filter' => ['like', 'yuriy']
              ])
              ->getRawSql(),
      "select users.name from users where users.name like '%yuriy%'"
    );

    $this->assertEquals(
      User::find()
              ->field('name', [
                'filter' => ['like', null]
              ])
              ->getRawSql(),
      "select users.name from users"
    );
  }

  public function testOrder(): void
  {
    $this->assertEquals(
      User::find()
              ->field('id', [
                'asc' => []
              ])
              ->field('name')
              ->getRawSql(),
      'select users.id, users.name from users order by users.id asc'
    );
  }

  public function testFunction(): void
  {
    $this->assertEquals(
      User::find()
              ->field('id', [
                'max' => []
              ])
              ->getRawSql(),
      'select max(users.id) from users'
    );
  }

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
