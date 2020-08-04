<?php declare(strict_types = 1);
use core\SuQLSpecialSymbols;
use PHPUnit\Framework\TestCase;

final class SuQLTest extends TestCase
{
  private $suql;

  private function init()
  {
    $this->suql = new SuQL;

    $this->suql->rel(['users' => 'u'], ['user_group' => 'ug'], 'u.id = ug.user_id');
    $this->suql->rel(['user_group' => 'ug'], ['groups' => 'g'], 'ug.group_id = g.id');

    $this->suql->setAdapter('mysql');
  }

  public function testSelect(): void
  {
    $this->init();

    $this->assertEquals(
      $this->suql->query('
        SELECT FROM users
          id,
          name
        ;
      ')->getSQL(),
      'select users.id, users.name from users'
    );
    $this->assertNull($this->suql->getSQL());

    $this->assertEquals(
      $this->suql->query('
        SELECT FROM users
          *
        ;
      ')->getSQL(),
      'select users.* from users'
    );
    $this->assertNull($this->suql->getSQL());

    $this->assertEquals(
      $this->suql->query('
        SELECT FROM users
          id@uid,
          name@uname
        ;
      ')->getSQL(),
      'select users.id as uid, users.name as uname from users'
    );
    $this->assertNull($this->suql->getSQL());
  }

  public function testSelectWhere(): void
  {
    $this->init();

    $this->assertEquals(
      $this->suql->query('
        SELECT FROM users
          id@uid,
          name@uname
        WHERE uid % 2 = 0;
      ')->getSQL(),
      'select users.id as uid, users.name as uname from users where users.id % 2 = 0'
    );
    $this->assertNull($this->suql->getSQL());

    $this->assertEquals(
      $this->suql->query('
        @users_belong_to_any_group = SELECT DISTINCT FROM user_group
                                      user_id
                                     ;
        SELECT FROM users
          id@uid,
          name
        WHERE uid not in @users_belong_to_any_group;
      ')->getSQL(),
      'select users.id as uid, users.name from users where users.id not in (select distinct user_group.user_id from user_group)'
    );
    $this->assertNull($this->suql->getSQL());
  }

  public function testSelectLimit(): void
  {
    $this->init();

    $this->assertEquals(
      $this->suql->query('
        SELECT FROM users
          *
        LIMIT 0, 2;
      ')->getSQL(),
      'select users.* from users limit 2'
    );
    $this->assertNull($this->suql->getSQL());
  }

  public function testSelectDistinct(): void
  {
    $this->init();

    $this->assertEquals(
      $this->suql->query('
        SELECT DISTINCT FROM users
          name
        ;
      ')->getSQL(),
      'select distinct users.name from users'
    );
    $this->assertNull($this->suql->getSQL());
  }

  public function testSelectJoin(): void
  {
    $this->init();

    $this->assertEquals(
      $this->suql->query('
        SELECT FROM users
        INNER JOIN user_group
        INNER JOIN groups
          id@gid,
          name@gname
        ;
      ')->getSQL(),
      'select '.
        'groups.id as gid, '.
        'groups.name as gname '.
      'from users '.
      'inner join user_group on users.id = user_group.user_id '.
      'inner join groups on user_group.group_id = groups.id'
    );
    $this->assertNull($this->suql->getSQL());

    // join and where
    $this->assertEquals(
      $this->suql->query('
        SELECT FROM users
          id,
          registration
        INNER JOIN user_group
        INNER JOIN groups
          name@group
        WHERE group = \'admin\';
      ')->getSQL(),
      'select '.
        'users.id, '.
        'users.registration, '.
        'groups.name as group '.
      'from users '.
      'inner join user_group on users.id = user_group.user_id '.
      'inner join groups on user_group.group_id = groups.id '.
      'where groups.name = \'admin\''
    );
    $this->assertNull($this->suql->getSQL());
  }

  public function testSelectGroup(): void
  {
    $this->init();

    $this->assertEquals(
      $this->suql->query('
        SELECT FROM users
        INNER JOIN user_group
        INNER JOIN groups
          name@gname,
          name.group.count@count
        WHERE gname = \'admin\';
      ')->getSQL(),
      'select '.
        'groups.name as gname, '.
        'count(groups.name) as count '.
      'from users '.
      'inner join user_group on users.id = user_group.user_id '.
      'inner join groups on user_group.group_id = groups.id '.
      'where groups.name = \'admin\' '.
      'group by groups.name'
    );
    $this->assertNull($this->suql->getSQL());
  }

  public function testNestedQueries(): void
  {
    $this->init();

    $this->assertEquals(
      $this->suql->query('
        @allGroupCount = SELECT FROM users
                         INNER JOIN user_group
                         INNER JOIN groups
                           name@gname,
                           name.group.count@count
                         ;
        SELECT FROM allGroupCount
          gname,
          count
        WHERE gname = \'admin\';
      ')->getSQL(),
      'select '.
        'allGroupCount.gname, '.
        'allGroupCount.count '.
      'from ('.
        'select '.
          'groups.name as gname, '.
          'count(groups.name) as count '.
        'from users '.
        'inner join user_group on users.id = user_group.user_id '.
        'inner join groups on user_group.group_id = groups.id '.
        'group by groups.name'.
      ') allGroupCount '.
      'where gname = \'admin\''
    );
    $this->assertNull($this->suql->getSQL());
  }

  public function testSorting(): void
  {
    $this->init();

    $this->assertEquals(
      $this->suql->query('
        SELECT FROM users
        INNER JOIN user_group
        INNER JOIN groups
          name@gname,
          name.group.count.asc@count
        ;
      ')->getSQL(),
      'select '.
        'groups.name as gname, '.
        'count(groups.name) as count '.
      'from users '.
      'inner join user_group on users.id = user_group.user_id '.
      'inner join groups on user_group.group_id = groups.id '.
      'group by groups.name '.
      'order by count asc'
    );
    $this->assertNull($this->suql->getSQL());
  }

  public function testUnion(): void
  {
    $this->init();

    $this->assertEquals(
      $this->suql->query('
        @firstRegisration = SELECT FROM users
                              registration.min@reg_interval
                            ;
        @lastRegisration = SELECT FROM users
                             registration.max@reg_interval
                           ;
        @main = @firstRegisration union @lastRegisration;
      ')->getSQL(),
      '(select min(users.registration) as reg_interval from users) '.
        'union '.
      '(select max(users.registration) as reg_interval from users)'
    );
    $this->assertNull($this->suql->getSQL());
  }
}
