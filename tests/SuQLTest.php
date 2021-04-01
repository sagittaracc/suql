<?php declare(strict_types = 1);
use core\SuQLSpecialSymbols;
use PHPUnit\Framework\TestCase;
use sagittaracc\model\User;
use sagittaracc\model\UserGroup;
use sagittaracc\model\Group;
use sagittaracc\model\UserGroupView;
use SuQLGroupModifier;

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
}
