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
      "select users.id from users order by users.id desc",
      SuQL::toSql("
        users {
          id,
          id@uid.desc
        };
      ")
    );
  }
}
