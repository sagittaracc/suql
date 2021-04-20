<?php declare(strict_types = 1);
use core\SuQLSpecialSymbols;
use PHPUnit\Framework\TestCase;
use app\model\User;
use app\model\UserGroup;
use app\model\Group;
use app\model\UserGroupView;

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
      User::find()->field('id')->field('name')->getRawSql(),
      'select users.id, users.name from users'
    );

    // Set aliases for the fields
    $this->assertEquals(
      User::find()->field(['id' => 'uid'])->field(['name' => 'uname'])->getRawSql(),
      'select users.id as uid, users.name as uname from users'
    );

    // Select raw expression
    $this->assertEquals(
      RawSuQL::find()->field('2 * 2')->field("'Yuriy' as author")->getRawSql(),
      "select 2 * 2, 'Yuriy' as author"
    );

    // Select raw within a real model
    $this->assertEquals(
      User::find()->field('id')->raw('2 * 2')->getRawSql(),
      'select users.id, 2 * 2 from users'
    );

    $this->assertEquals(
      User::find()->select(['id', 'name'])->getRawSql(),
      'select users.id, users.name from users'
    );

    $this->assertEquals(
      User::find()
              ->select([
                'id' => 'uid',
                'name' => 'uname'
              ])->getRawSql(),
      'select users.id as uid, users.name as uname from users'
    );
  }

  public function testLimit(): void
  {
    $this->assertEquals(
      User::find()->limit(0, 3)->getRawSql(),
      'select * from users limit 3'
    );

    $this->assertEquals(
      User::find()->limit(3, 6)->getRawSql(),
      'select * from users limit 3, 6'
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

  public function testJoinWithView(): void
  {
    $this->assertEquals(
      User::find()->join(UserGroupView::class)->getRawSql(),
      'select * from users '.
      'inner join ('.
        'select users.id as uid, users.name as uname, groups.id as gid, groups.name as gname from users '.
        'inner join user_group on users.id = user_group.user_id '.
        'inner join groups on user_group.group_id = groups.id'.
      ') app_model_UserGroupView on users.id = app_model_UserGroupView.user_id'
    );
  }

  public function testModifiers(): void
  {
    $this->assertEquals(
      User::find()->join(UserGroup::class)
                  ->join(Group::class)
                    ->field('name')
                    ->field(['name' => 'count'], [
                      'group',
                      'count'
                    ])
                  ->getRawSql(),
      'select groups.name, count(groups.name) as count from users inner join user_group on users.id = user_group.user_id inner join groups on user_group.group_id = groups.id group by groups.name'
    );
  }

  public function testView(): void
  {
    $this->assertEquals(
      UserGroupView::find()->select(['uid', 'uname'])->getRawSql(),
      'select '.
        'app_model_UserGroupView.uid, '.
        'app_model_UserGroupView.uname '.
      'from ('.
        'select '.
          'users.id as uid, '.
          'users.name as uname, '.
          'groups.id as gid, '.
          'groups.name as gname '.
        'from users '.
        'inner join user_group on users.id = user_group.user_id '.
        'inner join groups on user_group.group_id = groups.id'.
      ') app_model_UserGroupView'
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
      "select users.name, users.id from users where users.name like :ph_12cb8fae9701df6e8e8b1b972362a7ff and users.id > :ph_fc02896e3034a4ed53259916e2e2d82d"
    );

    // Where by filters
    $this->assertEquals(
      User::find()
              ->field('name', [
                'filter' => ['like', 'yuriy']
              ])
              ->getRawSql(),
      "select users.name from users where users.name like :ph_12cb8fae9701df6e8e8b1b972362a7ff"
    );

    $this->assertEquals(
      User::find()
              ->field('name', [
                'filter' => ['like', null]
              ])
              ->getRawSql(),
      "select users.name from users"
    );

    // TODO: Вариант для WHERE EXISTS (через модификатор)
    // $this->assertEquals(
    //   User::find()
    //           ->field('id', [
    //             'exists' => [UserGroup::find()]
    //           ])
    //           ->getRawSql(),
    //   "select id from users where exists (select * from user_group where user_id = users.id)"
    // );
  }

  public function testHaving(): void
  {
    $this->assertEquals(
      User::find()
              ->field('id', [
                'having' => ['$ > 3']
              ])
              ->getRawSql(),
      'select users.id from users having id > 3'
    );
  }

  public function testOrder(): void
  {
    $this->assertEquals(
      User::find()
              ->field('id', [
                'asc'
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
                'max'
              ])
              ->getRawSql(),
      'select max(users.id) from users'
    );
  }

  public function testQueryModifier(): void
  {
    $this->assertEquals(
      User::find()->distinct()->getRawSql(),
      'select distinct * from users'
    );

    $this->assertEquals(
      User::find()->distinct()->field('name')->getRawSql(),
      'select distinct users.name from users'
    );
  }

  public function testUserModelExtension(): void
  {
    $this->assertEquals(
      User::find()->new()->getRawSql(),
      'select users.* from users where DATE(users.register) = CURDATE()'
    );

    $this->assertEquals(
      User::find()->field(['name' => 'user_name'], [
        'ucname'
      ])->getRawSql(),
      'select CONCAT(UCASE(LEFT(users.name, 1)), SUBSTRING(users.name, 2)) as user_name from users'
    );
  }

  public function testCallbackModifier(): void
  {
    $this->assertEquals(
      User::find()->field('id', function($ofield){
        $ofield->getOSelect()->addWhere("{$ofield->getField()} > 5");
      })->getRawSql(),
      'select users.id from users where users.id > 5'
    );
  }

  public function testInsert(): void
  {
    $user = new User([
      'id' => 1,
      'name' => 'Yuriy',
    ]);

    $this->assertEquals(
      $user->getRawSql(),
      "insert into users (id,name) values (1,'Yuriy')"
    );
  }

  public function testInsertWithPlaceholder(): void
  {
    $user = new User(['id', 'name']);

    $this->assertEquals(
      $user->getRawSql(),
      'insert into users (id,name) values (:id,:name)'
    );
  }

  public function testConditionModifier(): void
  {
    $this->assertEquals(
      User::find()->field(['id' => 'empty'], [
        'ifNull' => [1, 0]
      ])->getRawSql(),
      'select if(users.id is null, 1, 0) as empty from users'
    );
  }
}
