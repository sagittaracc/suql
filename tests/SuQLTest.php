<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;

final class SuQLTest extends TestCase
{
  public function testSelect(): void
  {
    $this->assertEquals(
      'select users.* from users',
      SuQL::toSql('users {*};')
    );
  }

  public function testSelectFields(): void
  {
    $this->assertEquals(
      'select users.id as uid, users.name as uname from users',
      SuQL::toSql('users {id@uid, name@uname};')
    );
  }

  public function testSelectWhere(): void
  {
    $this->assertEquals(
      "select users.id as uid, users.name as uname from users where users.id > 5 and users.name <> 'admin'",
      SuQL::toSql("
        users {
          id@uid,
          name@uname
        } ~ uid > 5 and uname <> 'admin';
      ")
    );
  }

  public function testSelectAllFieldsUsingWhere(): void
  {
    $this->assertEquals(
      "select users.*, users.id as uid from users where users.id > 5",
      SuQL::toSql("
        users {
          *,
          id@uid
        } ~ uid > 5;
      ")
    );
  }

  public function testGroup(): void
  {
    $this->assertEquals(
      "select groups.name, count(groups.name) as cnt from groups group by groups.name",
      SuQL::toSql("
        groups {
          name,
          name@cnt.group.count
        };
      ")
    );
  }

  public function testJoinGroup(): void
  {
    $this->assertEquals(
      "select users.id as uid, groups.id as gid, groups.name as gname, count(groups.name) as cnt from users inner join user_group on users.id  =  user_id inner join groups on group_id  =  groups.id group by groups.name",
      SuQL::toSql("
        users {
          id@uid
        }
        [uid <--> user_id]
        user_group {}
        [group_id <--> gid]
        groups {
          id@gid,
          name@gname,
          name@cnt.group.count
        };
      ")
    );
  }

  public function testOrder(): void
  {
    $this->assertEquals(
      "select users.name as uname, users.id as uid from users order by users.name desc, users.id asc",
      SuQL::toSql("
        users {
          name@uname.desc,
          id@uid.asc
        };
      ")
    );
  }

  public function testSQLObject(): void
  {
    $this->assertEquals(
      [
        'queries' => [
          'main' => [
            'select' => [
              't1.gname' => ['field' => 't1.gname', 'alias' => ''],
              't1.cnt' => ['field' => 't1.cnt', 'alias' => ''],
            ],
            'from' => 't1',
            'where' => [],
            'join' => [],
            'group' => [],
            'order' => [],
          ],
          't1' => [
            'select' => [
              'users.id as uid' => ['field' => 'users.id', 'alias' => 'uid'],
              'groups.id as gid' => ['field' => 'groups.id', 'alias' => 'gid'],
              'groups.name as gname' => ['field' => 'groups.name', 'alias' => 'gname'],
              'groups.name as cnt' => [
                'field' => 'groups.name',
                'alias' => 'cnt',
                'modifier' => [
                  'group' => [],
                  'count' => []
                ]
              ],
            ],
            'from' => 'users',
            'where' => [
              0 => 'uid > 1',
              1 => 'gid > 2',
            ],
            'join' => [
              0 => ['table' => 'user_group', 'on' => 'uid <--> user_id'],
              1 => ['table' => 'groups', 'on' => 'group_id <--> gid'],
            ],
            'group' => [],
            'order' => []
          ]
        ]
      ],
      SuQL::toSqlObject("
        #t1 = users {
          id@uid
        } ~ uid > 1
        [uid <--> user_id]
        user_group {}
        [group_id <--> gid]
        groups {
          id@gid,
          name@gname,
          name@cnt.group.count
        } ~ gid > 2;

        t1 {
          gname,
          cnt
        };
      ")
    );
  }

  public function testNestedQuery(): void
  {
    $this->assertEquals(
      'select t1.gname, t1.cnt from (select users.id as uid, groups.id as gid, groups.name as gname, count(groups.name) as cnt from users inner join user_group on users.id  =  user_id inner join groups on group_id  =  groups.id where users.id > 1 and groups.id > 2 group by groups.name) t1',
      SuQL::toSql("
        #t1 = users {
          id@uid
        } ~ uid > 1
        [uid <--> user_id]
        user_group {}
        [group_id <--> gid]
        groups {
          id@gid,
          name@gname,
          name@cnt.group.count
        } ~ gid > 2;

        t1 {
          gname,
          cnt
        };
      ")
    );
  }
}
