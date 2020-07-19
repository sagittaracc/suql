<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;

use core\SuQLObject;

final class SuQLObjectTest extends TestCase
{
  private $db;

  private function init()
  {
    $this->db = new SuQLObject;

    $this->db->rel(['users' => 'u'], ['user_group' => 'ug'], 'u.id = ug.user_id');
    $this->db->rel(['user_group' => 'ug'], ['groups' => 'g'], 'ug.group_id = g.id');

    $this->db->setAdapter('mysql');
  }

  public function testSelect(): void
  {
    $this->init();

    // Fetching by a field list
    $this->db->addSelect('main');
    $this->db->getQuery('main')->addFrom('users');
    $this->db->getQuery('main')->addField('users', 'id');
    $this->db->getQuery('main')->addField('users', 'name');

    $this->assertEquals($this->db->getSQL('all'), 'select users.id, users.name from users');
    $this->assertNull($this->db->getSQL('all'));

    // Fetching all the fields
    $this->db->addSelect('main');
    $this->db->getQuery('main')->addFrom('users');
    $this->db->getQuery('main')->addField('users', '*');
    $this->assertEquals($this->db->getSQL(['main']), 'select users.* from users');
    $this->assertNull($this->db->getSQL(['main']));

    // Fetching some fields with aliases
    $this->db->addSelect('main');
    $this->db->getQuery('main')->addFrom('users');
    $this->db->getQuery('main')->addField('users', ['id' => 'uid']);
    $this->db->getQuery('main')->addField('users', 'name@uname'); // just another way to set an alias
    $this->assertEquals($this->db->getSQL('all'), 'select users.id as uid, users.name as uname from users');
    $this->assertNull($this->db->getSQL(['main']));
  }

  public function testSelectWhere(): void
  {
    $this->init();

    $this->db->addSelect('main');
    $this->db->getQuery('main')->addFrom('users');
    $this->db->getQuery('main')->addField('users', ['id' => 'uid']);
    $this->db->getQuery('main')->addField('users', ['name' => 'uname']);
    $this->db->getQuery('main')->addWhere('uid % 2 = 0');
    $this->assertEquals($this->db->getSQL(['main']), 'select users.id as uid, users.name as uname from users where users.id % 2 = 0');
    $this->assertNull($this->db->getSQL(['main']));

    $this->db->addSelect('users_belong_to_any_group');
    $this->db->getQuery('users_belong_to_any_group')->addModifier('distinct');
    $this->db->getQuery('users_belong_to_any_group')->addFrom('user_group');
    $this->db->getQuery('users_belong_to_any_group')->addField('user_group', 'user_id');
    $this->db->addSelect('main');
    $this->db->getQuery('main')->addFrom('users');
    $this->db->getQuery('main')->addField('users', 'id@uid');
    $this->db->getQuery('main')->addField('users', 'name');
    $this->db->getQuery('main')->addWhere('uid not in @users_belong_to_any_group');
    $this->assertEquals($this->db->getSQL(['main']), 'select users.id as uid, users.name from users where users.id not in (select distinct user_group.user_id from user_group)');
    $this->assertNull($this->db->getSQL('all'));
  }

  public function testSelectLimit(): void
  {
    $this->init();

    $this->db->addSelect('main');
    $this->db->getQuery('main')->addFrom('users');
    $this->db->getQuery('main')->addField('users', '*');
    $this->db->getQuery('main')->addOffset(0);
    $this->db->getQuery('main')->addLimit(2);
    $this->assertEquals($this->db->getSQL(['main']), 'select users.* from users limit 2');
    $this->assertNull($this->db->getSQL(['main']));
  }

  public function testSelectDistinct(): void
  {
    $this->init();

    $this->db->addSelect('main');
    $this->db->getQuery('main')->addModifier('distinct');
    $this->db->getQuery('main')->addField('users', 'name');
    $this->db->getQuery('main')->addFrom('users');
    $this->assertEquals($this->db->getSQL('all'), 'select distinct users.name from users');
    $this->assertNull($this->db->getSQL('all'));
  }

  public function testSelectJoin(): void
  {
    $this->init();

    $this->db->addSelect('main');
    $this->db->getQuery('main')->addFrom('users');
    $this->db->getQuery('main')->addJoin('inner', 'user_group');
    $this->db->getQuery('main')->addJoin('inner', 'groups');
    $this->db->getQuery('main')->addField('groups', 'id@gid');
    $this->db->getQuery('main')->addField('groups', 'name@gname');
    $this->assertEquals($this->db->getSQL('all'),
      'select '.
        'groups.id as gid, '.
        'groups.name as gname '.
      'from users '.
      'inner join user_group on users.id = user_group.user_id '.
      'inner join groups on user_group.group_id = groups.id'
    );
    $this->assertNull($this->db->getSQL('all'));
  }

  public function testSelectGroup(): void
  {
    $this->init();

    $this->db->addSelect('main');
    $this->db->getQuery('main')->addFrom('users');
    $this->db->getQuery('main')->addJoin('inner', 'user_group');
    $this->db->getQuery('main')->addJoin('inner', 'groups');
    $this->db->getQuery('main')->addField('groups', 'name@gname');
    $this->db->getQuery('main')->addField('groups', 'name@count');
    $this->db->getQuery('main')->getField('groups', 'name@count')->addModifier('group');
    $this->db->getQuery('main')->getField('groups', 'name@count')->addModifier('count');
    $this->db->getQuery('main')->addWhere("gname = 'admin'");
    $this->assertEquals($this->db->getSQL('all'),
      'select '.
        'groups.name as gname, '.
        'count(groups.name) as count '.
      'from users '.
      'inner join user_group on users.id = user_group.user_id '.
      'inner join groups on user_group.group_id = groups.id '.
      'where groups.name = \'admin\' '.
      'group by groups.name'
    );
    $this->assertNull($this->db->getSQL('all'));
  }

  public function testNestedQueries(): void
  {
    $this->init();

    $this->db->addSelect('allGroupCount');
    $this->db->getQuery('allGroupCount')->addFrom('users');
    $this->db->getQuery('allGroupCount')->addJoin('inner', 'user_group');
    $this->db->getQuery('allGroupCount')->addJoin('inner', 'groups');
    $this->db->getQuery('allGroupCount')->addField('groups', 'name@gname');
    $this->db->getQuery('allGroupCount')->addField('groups', 'name@count');
    $this->db->getQuery('allGroupCount')->getField('groups', 'name@count')->addModifier('group');
    $this->db->getQuery('allGroupCount')->getField('groups', 'name@count')->addModifier('count');
    $this->db->addSelect('main');
    $this->db->getQuery('main')->addFrom('allGroupCount');
    $this->db->getQuery('main')->addField('allGroupCount', 'gname');
    $this->db->getQuery('main')->addField('allGroupCount', 'count');
    $this->db->getQuery('main')->addWhere("gname = 'admin'");
    $this->assertEquals($this->db->getSQL(['main']),
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
    $this->assertNull($this->db->getSQL('all'));
  }

  public function testSorting(): void
  {
    $this->init();

    $this->db->addSelect('main');
    $this->db->getQuery('main')->addFrom('users');
    $this->db->getQuery('main')->addJoin('inner', 'user_group');
    $this->db->getQuery('main')->addJoin('inner', 'groups');
    $this->db->getQuery('main')->addField('groups', 'name@gname');
    $this->db->getQuery('main')->addField('groups', 'name@count');
    $this->db->getQuery('main')->getField('groups', 'name@count')->addModifier('group');
    $this->db->getQuery('main')->getField('groups', 'name@count')->addModifier('count');
    $this->db->getQuery('main')->getField('groups', 'name@count')->addModifier('asc');
    $this->assertEquals($this->db->getSQL('all'),
      'select '.
        'groups.name as gname, '.
        'count(groups.name) as count '.
      'from users '.
      'inner join user_group on users.id = user_group.user_id '.
      'inner join groups on user_group.group_id = groups.id '.
      'group by groups.name '.
      'order by count asc'
    );
    $this->assertNull($this->db->getSQL('all'));
  }
}
