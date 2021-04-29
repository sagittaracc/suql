<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;
use app\model\User;
use app\model\UserGroup;
use app\model\Group;
use app\model\UserGroupView;
use app\models\ModelWithAnUnsupportedDriver;
use suql\core\SuQLModifier;
use suql\core\SuQLPlaceholder;
use suql\exception\SqlDriverNotSupportedException;

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
      RawSuQL::select()->field('2 * 2')->field("'Yuriy' as author")->getRawSql(),
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
                'name'
              ])->getRawSql(),
      'select users.id as uid, users.name from users'
    );
  }

  public function testCaseWhen(): void
  {
    $this->assertEquals(
      User::find()
              ->field('id', ['test_case'])
              ->getRawSql(),
      "select case ".
                "when users.id = 1 then 'admin' ".
                "when users.id = 2 then 'user' ".
                "when users.id > 3 and groups.id < 10 then 'guest' ".
                "else 'nobody' ".
              "end ".
      "from users"
    );
  }

  public function testInlineModifiersApplied(): void
  {
    $this->assertEquals(
      User::find()
              ->select([
                'name',
                (new SuQLModifier('max'))->applyTo(['id' => 'max'])
              ])->getRawSql(),
      'select users.name, max(users.id) as max from users'
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

    $this->assertEquals(
      User::find()->join(UserGroup::class)
                  ->join(Group::class)
                    ->groupBy('name')
                    ->countBy(['name' => 'count'])
                  ->getRawSql(),
      'select groups.name, count(groups.name) as count from users inner join user_group on users.id = user_group.user_id inner join groups on user_group.group_id = groups.id group by groups.name'
    );
  }

  public function testView(): void
  {
    $this->assertEquals(
      UserGroupView::find()->select(['uid', 'uname' => 'vuname'])->getRawSql(),
      'select '.
        'app_model_UserGroupView.uid, '.
        'app_model_UserGroupView.uname as vuname '.
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
              ->select(['id', 'name'])
              ->where('id mod 2 = 0')
              ->getRawSql(),
      'select users.id, users.name from users where id mod 2 = 0'
    );

    $this->assertEquals(
      User::find()
              ->select(['id', 'name'])
              ->where([
                'id' => 1,
                'name' => 'users',
              ])
              ->getRawSql(),
      'select users.id, users.name from users where users.id = :ph_fc02896e3034a4ed53259916e2e2d82d and users.name = :ph_12cb8fae9701df6e8e8b1b972362a7ff'
    );

    $this->assertEquals(
      User::find()
              ->select(['id', 'name'])
              ->where(['id', 'greater', [1]])
              ->getRawSql(),
      'select users.id, users.name from users where users.id > :ph_fc02896e3034a4ed53259916e2e2d82d'
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

    $this->assertEquals(
      User::find()
              ->field('id', [
                'greater' => [new SuQLPlaceholder('id')]
              ])
              ->getRawSql(),
      'select users.id from users where users.id > :id'
    );

    // Where by filters
    $this->assertEquals(
      User::find()
              ->field('name', [
                'filter' => ['like', 'yuriy']
              ])
              ->getRawSql(),
      'select users.name from users where users.name like :ph_12cb8fae9701df6e8e8b1b972362a7ff'
    );

    $this->assertEquals(
      User::find()
              ->field('name', [
                'filter' => ['like', new SuQLPlaceholder('name')]
              ])
              ->getRawSql(),
      'select users.name from users where users.name like :name'
    );

    $this->assertEquals(
      User::find()
              ->field('name', [
                'filter' => ['like', null]
              ])
              ->getRawSql(),
      "select users.name from users"
    );

    $this->assertEquals(
      User::find()
              ->field('id', [
                'between' => [1, 2]
              ], false)
              ->getRawSql(),
      'select * from users where users.id between :ph_98aace064c30b09e0247de93e95303f7 and :ph_b05ba045acc2eef36fd0ed5bdb815bb5'
    );

    $this->assertEquals(
      User::find()
              ->field('id', [
                'between' => [new SuQLPlaceholder('sid'), 2]
              ], false)
              ->getRawSql(),
      'select * from users where users.id between :sid and :ph_82b64d4cd88a68a2f6dd1d94a52a3ecb'
    );

    $this->assertEquals(
      User::find()
              ->field('id', [
                'between' => [new SuQLPlaceholder('sid'), new SuQLPlaceholder('eid')]
              ], false)
              ->getRawSql(),
      'select * from users where users.id between :sid and :eid'
    );

    $this->assertEquals(
      User::find()
              ->field('id', [
                'in' => [1, 2, 3]
              ], false)
              ->getRawSql(),
      'select * from users where users.id in (:ph_98aace064c30b09e0247de93e95303f7,:ph_b05ba045acc2eef36fd0ed5bdb815bb5,:ph_15a024187fe0b56919f66cfc17f49dcf)'
    );

    $this->assertEquals(
      User::find()
              ->field('id', [
                'in' => [new SuQLPlaceholder('id1'), 2, 3]
              ], false)
              ->getRawSql(),
      'select * from users where users.id in (:id1,:ph_3edae988aed81e8ef4db1d63118c000d,:ph_3bc004e03e89da1b7ff4857ef4466802)'
    );

    $this->assertEquals(
      User::find()
              ->field('id', [
                'in' => [new SuQLPlaceholder('id1'), new SuQLPlaceholder('id2'), new SuQLPlaceholder('id3')]
              ], false)
              ->getRawSql(),
      'select * from users where users.id in (:id1,:id2,:id3)'
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

    $this->assertEquals(
      User::find()
              ->orderBy([
                'id' => 'asc',
                'name' => 'desc'
              ])
              ->getRawSql(),
      'select users.id, users.name from users order by users.id asc, users.name desc'
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

    $this->assertEquals(
      User::find()->max('id')->getRawSql(),
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

  public function testModelWithAnUnsupportedDriver(): void
  {
    $this->expectException(SqlDriverNotSupportedException::class);
    ModelWithAnUnsupportedDriver::find()->getRawSql();
  }

  public function testQueryParamList(): void
  {
    $query = User::find()
                ->field('id', [
                  'in' => [new SuQLPlaceholder('id'),2,3]
                ]);

    $sql = $query->getRawSql();
    $params = $query->getParamList();

    $this->assertEquals(
      $sql,
      'select '.
        'users.id '.
      'from users '.
      'where users.id in ('.
        ':id,'.
        ':ph_1c039cba3fa9c33e62bc68badc90d75b,'.
        ':ph_26844f60882164514b4a0e4a070cc63c'.
      ')'
    );

    $this->assertEquals(
      $params,
      [
        ':ph_1c039cba3fa9c33e62bc68badc90d75b' => 2,
        ':ph_26844f60882164514b4a0e4a070cc63c' => 3,
      ]
    );
  }
}
