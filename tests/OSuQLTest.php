<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;

final class OSuQLTest extends TestCase
{
  private $osuql;

  private function init()
  {
    $this->osuql = new OSuQL;

    $this->osuql->rel(['users' => 'u'], ['user_group' => 'ug'], 'u.id = ug.user_id');
    $this->osuql->rel(['user_group' => 'ug'], ['groups' => 'g'], 'ug.group_id = g.id');

    $this->osuql->setAdapter('mysql');
  }

  public function testSelect(): void
  {
    $this->init();

    $this->osuql->select()
                ->users()
                  ->field('id')
                  ->field('name');

    $this->assertEquals($this->osuql->getSQL(), 'select users.id, users.name from users');
    $this->assertNull($this->osuql->getSQL());

    $this->osuql->select()
                ->users()
                  ->field('*');

    $this->assertEquals($this->osuql->getSQL(), 'select users.* from users');
    $this->assertNull($this->osuql->getSQL());

    $this->osuql->select()
                ->users();

    $this->assertEquals($this->osuql->getSQL(), 'select * from users');
    $this->assertNull($this->osuql->getSQL());

    $this->osuql->select()
                ->users()
                  ->field(['id' => 'uid'])
                  ->field('name@uname');

    $this->assertEquals($this->osuql->getSQL(), 'select users.id as uid, users.name as uname from users');
    $this->assertNull($this->osuql->getSQL());
  }

  public function testSelectWhere(): void
  {
    $this->init();

    $this->osuql->select()
                ->users()
                  ->field(['id' => 'uid'])
                  ->field(['name' => 'uname'])
                ->where('uid % 2 = 0');

    $this->assertEquals($this->osuql->getSQL(), 'select users.id as uid, users.name as uname from users where users.id % 2 = 0');
    $this->assertNull($this->osuql->getSQL());

    $this->osuql->query('users_belong_to_any_group')
                ->select()
                  ->user_group('distinct')
                    ->field('user_id');
    $this->osuql->query()
              ->select()
                ->users()
                  ->field('id@uid')
                  ->field('name')
                ->where('uid not in @users_belong_to_any_group');

    $this->assertEquals($this->osuql->getSQL(), 'select users.id as uid, users.name from users where users.id not in (select distinct user_group.user_id from user_group)');
    $this->assertNull($this->osuql->getSQL());
  }

  public function testSelectLimit(): void
  {
    $this->init();

    $this->osuql->select()
                ->users()
                  ->field('*')
                ->offset(0)
                ->limit(2);

    $this->assertEquals($this->osuql->getSQL(), 'select users.* from users limit 2');
    $this->assertNull($this->osuql->getSQL());
  }

  public function testSelectDistinct(): void
  {
    $this->init();

    $this->osuql->select()
                ->users('distinct')
                  ->field('name');

    $this->assertEquals($this->osuql->getSQL(), 'select distinct users.name from users');
    $this->assertNull($this->osuql->getSQL());
  }

  public function testSelectJoin(): void
  {
    $this->init();

    $this->osuql->select()
                ->users()
                ->user_group()
                ->groups()
                  ->field(['id' => 'gid'])
                  ->field(['name' => 'gname']);

    $this->assertEquals($this->osuql->getSQL(),
      'select '.
        'groups.id as gid, '.
        'groups.name as gname '.
      'from users '.
      'inner join user_group on users.id = user_group.user_id '.
      'inner join groups on user_group.group_id = groups.id'
    );
    $this->assertNull($this->osuql->getSQL());

    // join and where
    $this->osuql->select()
                ->users()
                  ->field('id')
                  ->field('registration')
                ->user_group()
                ->groups()
                  ->field(['name' => 'group'])
              ->where("group = 'admin'");

    $this->assertEquals(
      $this->osuql->getSQL(),
      'select '.
        'users.id, '.
        'users.registration, '.
        'groups.name as group '.
      'from users '.
      'inner join user_group on users.id = user_group.user_id '.
      'inner join groups on user_group.group_id = groups.id '.
      'where groups.name = \'admin\''
    );
    $this->assertNull($this->osuql->getSQL());
  }

  public function testSelectGroup(): void
  {
    $this->init();

    $this->osuql->select()
                ->users()
                ->user_group()
                ->groups()
                  ->field('name@gname')
                  ->field('name@count')->group()->count()
                ->where("gname = 'admin'");

    $this->assertEquals($this->osuql->getSQL(),
      'select '.
        'groups.name as gname, '.
        'count(groups.name) as count '.
      'from users '.
      'inner join user_group on users.id = user_group.user_id '.
      'inner join groups on user_group.group_id = groups.id '.
      'where groups.name = \'admin\' '.
      'group by groups.name'
    );
    $this->assertNull($this->osuql->getSQL());
  }

  public function testNestedQueries(): void
  {
    $this->init();

    $this->osuql->query('allGroupCount')
                ->select()
                  ->users()
                  ->user_group()
                  ->groups()
                    ->field('name@gname')
                    ->field('name@count')->group()->count();
    $this->osuql->query()
                ->select()
                  ->allGroupCount()
                    ->field('gname')
                    ->field('count')
                  ->where("gname = 'admin'");

    $this->assertEquals($this->osuql->getSQL(),
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
    $this->assertNull($this->osuql->getSQL());
  }

  public function testSorting(): void
  {
    $this->init();

    $this->osuql->select()
                ->users()
                ->user_group()
                ->groups()
                  ->field('name@gname')
                  ->field('name@count')->group()->count()->asc();

    $this->assertEquals($this->osuql->getSQL(),
      'select '.
        'groups.name as gname, '.
        'count(groups.name) as count '.
      'from users '.
      'inner join user_group on users.id = user_group.user_id '.
      'inner join groups on user_group.group_id = groups.id '.
      'group by groups.name '.
      'order by count asc'
    );
    $this->assertNull($this->osuql->getSQL());
  }

  public function testUnion(): void
  {
    $this->init();

    $this->osuql->query('firstRegisration')
                ->select()
                  ->users()
                    ->field('registration@reg_interval')->min();
    $this->osuql->query('lastRegisration')
                ->select()
                  ->users()
                    ->field('registration@reg_interval')->max();
    $this->osuql->query()
                ->union('@firstRegisration')
                ->union('@lastRegisration');

    $this->assertEquals($this->osuql->getSQL(),
      '(select min(users.registration) as reg_interval from users) '.
        'union '.
      '(select max(users.registration) as reg_interval from users)'
    );
    $this->assertNull($this->osuql->getSQL());
  }
}
