<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;

final class SuQLTest extends TestCase
{
  public function testSelect(): void
  {
    $db = (new SuQL)->setAdapter('mysql');

    $this->assertEquals(
      'select users.* from users',
      $db->query("
        SELECT FROM users
          *
        ;
      ")->getSQL()
    );
  }

  public function testSelectDistinct(): void
  {
    $db = (new SuQL)->setAdapter('mysql');

    $this->assertEquals(
      'select distinct users.id from users',
      $db->query("
        SELECT DISTINCT FROM users
          id
        ;
      ")->getSQL()
    );
  }

  public function testSelectFields(): void
  {
    $db = (new SuQL)->setAdapter('mysql');

    $this->assertEquals(
      'select users.id as uid, users.name as uname from users',
      $db->query("
        SELECT FROM users
          id@uid,
          name@uname
        ;
      ")->getSQL()
    );
  }

  public function testSelectWhere(): void
  {
    $db = (new SuQL)->setAdapter('mysql');

    $this->assertEquals(
      "select users.id as uid, users.name as uname from users where users.id > 5 and users.name <> 'admin'",
      $db->query("
        SELECT FROM users
          id@uid,
          name@uname
        WHERE uid > 5 and uname <> 'admin'
        ;
      ")->getSQL()
    );
  }

  public function testJoinGroup(): void
  {
    $db = (new SuQL())->setAdapter('mysql');

    $db->rel(['users' => 'a'], ['user_group' => 'b'], 'a.id = b.user_id');
    $db->rel(['user_group' => 'a'], ['groups' => 'b'], 'a.group_id = b.id');

    $this->assertEquals(
      "select allUsers.gname, allUsers.cnt ".
      "from (".
        "select ".
          "groups.name as gname, ".
          "count(groups.name) as cnt ".
        "from users ".
        "inner join user_group on users.id = user_group.user_id ".
        "inner join groups on user_group.group_id = groups.id ".
        "group by groups.name".
      ") allUsers ".
      "where gname = 'admin'"
      ,
      $db->query("
        @allUsers = SELECT FROM users
                    INNER JOIN user_group
                    INNER JOIN groups
                      name@gname
                      name.group.count@cnt
                    ;

        SELECT FROM @allUsers
          gname,
          cnt
        WHERE gname = 'admin'
        ;
      ")->getSQL()
    );
  }
}
