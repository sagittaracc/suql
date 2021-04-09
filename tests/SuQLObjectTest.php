<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;

use core\SuQLObject;
use core\SuQLError;

final class SuQLObjectTest extends TestCase
{
  private $osuql;

  private function init()
  {
    $this->osuql = new SuQLObject;

    $this->osuql->rel(['users' => 'u'], ['user_group' => 'ug'], 'u.id = ug.user_id');
    $this->osuql->rel(['user_group' => 'ug'], ['groups' => 'g'], 'ug.group_id = g.id');

    $this->osuql->setDriver('mysql');
  }

  public function testSelect(): void
  {
    $this->init();

    // Fetching by a field list
    $this->osuql->addSelect('main');
    $this->osuql->getQuery('main')->addFrom('users');
    $this->osuql->getQuery('main')->addField('users', 'id');
    $this->osuql->getQuery('main')->addField('users', 'name');

    $this->assertEquals($this->osuql->getSQL('all'), 'select users.id, users.name from users');
    $this->assertNull($this->osuql->getSQL('all'));

    // Fetching all the fields
    $this->osuql->addSelect('main');
    $this->osuql->getQuery('main')->addFrom('users');
    $this->osuql->getQuery('main')->addField('users', '*');
    $this->assertEquals($this->osuql->getSQL(['main']), 'select users.* from users');
    $this->assertNull($this->osuql->getSQL(['main']));

    $this->osuql->addSelect('main');
    $this->osuql->getQuery('main')->addFrom('users');
    $this->assertEquals($this->osuql->getSQL(['main']), 'select * from users');
    $this->assertNull($this->osuql->getSQL(['main']));

    // Fetching some fields with aliases
    $this->osuql->addSelect('main');
    $this->osuql->getQuery('main')->addFrom('users');
    $this->osuql->getQuery('main')->addField('users', ['id' => 'uid']);
    $this->osuql->getQuery('main')->addField('users', 'name@uname'); // just another way to set an alias
    $this->assertEquals($this->osuql->getSQL('all'), 'select users.id as uid, users.name as uname from users');
    $this->assertNull($this->osuql->getSQL(['main']));

    // Select raw expression
    $this->osuql->addSelect('main');
    $this->osuql->getQuery('main')->addField(null, "2 * 2");
    $this->osuql->getQuery('main')->addField(null, "'Yuriy' as author");
    $this->assertEquals($this->osuql->getSQL('all'), "select 2 * 2, 'Yuriy' as author");
    $this->assertNull($this->osuql->getSQL(['main']));
  }

  public function testSelectWhere(): void
  {
    $this->init();

    $this->osuql->addSelect('main');
    $this->osuql->getQuery('main')->addFrom('users');
    $this->osuql->getQuery('main')->addField('users', ['id' => 'uid']);
    $this->osuql->getQuery('main')->addField('users', ['name' => 'uname']);
    $this->osuql->getQuery('main')->addWhere('uid % 2 = 0');
    $this->assertEquals($this->osuql->getSQL(['main']), 'select users.id as uid, users.name as uname from users where users.id % 2 = 0');
    $this->assertNull($this->osuql->getSQL(['main']));

    $this->osuql->addSelect('users_belong_to_any_group');
    $this->osuql->getQuery('users_belong_to_any_group')->addModifier('distinct');
    $this->osuql->getQuery('users_belong_to_any_group')->addFrom('user_group');
    $this->osuql->getQuery('users_belong_to_any_group')->addField('user_group', 'user_id');
    $this->osuql->addSelect('main');
    $this->osuql->getQuery('main')->addFrom('users');
    $this->osuql->getQuery('main')->addField('users', 'id@uid');
    $this->osuql->getQuery('main')->addField('users', 'name');
    $this->osuql->getQuery('main')->addWhere('uid not in @users_belong_to_any_group');
    $this->assertEquals($this->osuql->getSQL(['main']), 'select users.id as uid, users.name from users where users.id not in (select distinct user_group.user_id from user_group)');
    $this->assertNull($this->osuql->getSQL('all'));
  }

  public function testSelectLimit(): void
  {
    $this->init();

    $this->osuql->addSelect('main');
    $this->osuql->getQuery('main')->addFrom('users');
    $this->osuql->getQuery('main')->addField('users', '*');
    $this->osuql->getQuery('main')->addOffset(0);
    $this->osuql->getQuery('main')->addLimit(2);
    $this->assertEquals($this->osuql->getSQL(['main']), 'select users.* from users limit 2');
    $this->assertNull($this->osuql->getSQL(['main']));
  }

  public function testSelectDistinct(): void
  {
    $this->init();

    $this->osuql->addSelect('main');
    $this->osuql->getQuery('main')->addModifier('distinct');
    $this->osuql->getQuery('main')->addField('users', 'name');
    $this->osuql->getQuery('main')->addFrom('users');
    $this->assertEquals($this->osuql->getSQL('all'), 'select distinct users.name from users');
    $this->assertNull($this->osuql->getSQL('all'));
  }

  public function testSelectJoin(): void
  {
    $this->init();

    $this->osuql->addSelect('main');
    $this->osuql->getQuery('main')->addFrom('users');
    $this->osuql->getQuery('main')->addJoin('inner', 'user_group');
    $this->osuql->getQuery('main')->addJoin('inner', 'groups');
    $this->osuql->getQuery('main')->addField('groups', 'id@gid');
    $this->osuql->getQuery('main')->addField('groups', 'name@gname');
    $this->assertEquals($this->osuql->getSQL('all'),
      'select '.
        'groups.id as gid, '.
        'groups.name as gname '.
      'from users '.
      'inner join user_group on users.id = user_group.user_id '.
      'inner join groups on user_group.group_id = groups.id'
    );
    $this->assertNull($this->osuql->getSQL('all'));

    // join and where
    $this->osuql->addSelect('main');
    $this->osuql->getQuery('main')->addFrom('users');
    $this->osuql->getQuery('main')->addField('users', 'id');
    $this->osuql->getQuery('main')->addField('users', 'registration');
    $this->osuql->getQuery('main')->addJoin('inner', 'user_group');
    $this->osuql->getQuery('main')->addJoin('inner', 'groups');
    $this->osuql->getQuery('main')->addField('groups', ['name' => 'group']);
    $this->osuql->getQuery('main')->addWhere("group = 'admin'");
    $this->assertEquals($this->osuql->getSQL('all'),
      'select '.
        'users.id, '.
        'users.registration, '.
        'groups.name as group '.
      'from users '.
      'inner join user_group on users.id = user_group.user_id '.
      'inner join groups on user_group.group_id = groups.id '.
      'where groups.name = \'admin\''
    );
    $this->assertNull($this->osuql->getSQL('all'));

    $this->osuql->rel(['users' => 'u'], ['view' => 'v'], 'u.id = v.id');
    $this->osuql->addSelect('main');
    $this->osuql->getQuery('main')->addFrom('users');
    $this->osuql->getQuery('main')->addField('users', 'id');
    $this->osuql->getQuery('main')->addJoin('inner', 'view');
    $this->osuql->addSelect('view');
    $this->osuql->getQuery('view')->addFrom('users');
    $this->osuql->getQuery('view')->addField('users', 'id');
    $this->assertEquals($this->osuql->getSQL(['main']),
      'select '.
        'users.id '.
      'from users '.
      'inner join ('.
        'select '.
          'users.id '.
        'from users'.
      ') view on users.id = view.id'
    );
  }

  public function testSelectGroup(): void
  {
    $this->init();

    $this->osuql->addSelect('main');
    $this->osuql->getQuery('main')->addFrom('users');
    $this->osuql->getQuery('main')->addJoin('inner', 'user_group');
    $this->osuql->getQuery('main')->addJoin('inner', 'groups');
    $this->osuql->getQuery('main')->addField('groups', 'name@gname');
    $this->osuql->getQuery('main')->addField('groups', 'name@count');
    $this->osuql->getQuery('main')->getField('groups', 'name@count')->addModifier('group');
    $this->osuql->getQuery('main')->getField('groups', 'name@count')->addModifier('count');
    $this->osuql->getQuery('main')->addWhere("gname = 'admin'");
    $this->assertEquals($this->osuql->getSQL('all'),
      'select '.
        'groups.name as gname, '.
        'count(groups.name) as count '.
      'from users '.
      'inner join user_group on users.id = user_group.user_id '.
      'inner join groups on user_group.group_id = groups.id '.
      'where groups.name = \'admin\' '.
      'group by groups.name'
    );
    $this->assertNull($this->osuql->getSQL('all'));
  }

  public function testNestedQueries(): void
  {
    $this->init();

    $this->osuql->addSelect('allGroupCount');
    $this->osuql->getQuery('allGroupCount')->addFrom('users');
    $this->osuql->getQuery('allGroupCount')->addJoin('inner', 'user_group');
    $this->osuql->getQuery('allGroupCount')->addJoin('inner', 'groups');
    $this->osuql->getQuery('allGroupCount')->addField('groups', 'name@gname');
    $this->osuql->getQuery('allGroupCount')->addField('groups', 'name@count');
    $this->osuql->getQuery('allGroupCount')->getField('groups', 'name@count')->addModifier('group');
    $this->osuql->getQuery('allGroupCount')->getField('groups', 'name@count')->addModifier('count');
    $this->osuql->addSelect('main');
    $this->osuql->getQuery('main')->addFrom('allGroupCount');
    $this->osuql->getQuery('main')->addField('allGroupCount', 'gname');
    $this->osuql->getQuery('main')->addField('allGroupCount', 'count');
    $this->osuql->getQuery('main')->addWhere("gname = 'admin'");
    $this->assertEquals($this->osuql->getSQL(['main']),
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
    $this->assertNull($this->osuql->getSQL('all'));
  }

  public function testSorting(): void
  {
    $this->init();

    $this->osuql->addSelect('main');
    $this->osuql->getQuery('main')->addFrom('users');
    $this->osuql->getQuery('main')->addJoin('inner', 'user_group');
    $this->osuql->getQuery('main')->addJoin('inner', 'groups');
    $this->osuql->getQuery('main')->addField('groups', 'name@gname');
    $this->osuql->getQuery('main')->addField('groups', 'name@count');
    $this->osuql->getQuery('main')->getField('groups', 'name@count')->addModifier('group');
    $this->osuql->getQuery('main')->getField('groups', 'name@count')->addModifier('count');
    $this->osuql->getQuery('main')->getField('groups', 'name@count')->addModifier('asc');
    $this->assertEquals($this->osuql->getSQL('all'),
      'select '.
        'groups.name as gname, '.
        'count(groups.name) as count '.
      'from users '.
      'inner join user_group on users.id = user_group.user_id '.
      'inner join groups on user_group.group_id = groups.id '.
      'group by groups.name '.
      'order by count asc'
    );
    $this->assertNull($this->osuql->getSQL('all'));
  }

  public function testUnion(): void
  {
    $this->init();

    $this->osuql->addSelect('firstRegisration');
    $this->osuql->getQuery('firstRegisration')->addFrom('users');
    $this->osuql->getQuery('firstRegisration')->addField('users', 'registration@reg_interval');
    $this->osuql->getQuery('firstRegisration')->getField('users', 'registration@reg_interval')->addModifier('min');
    $this->osuql->addSelect('lastRegisration');
    $this->osuql->getQuery('lastRegisration')->addFrom('users');
    $this->osuql->getQuery('lastRegisration')->addField('users', 'registration@reg_interval');
    $this->osuql->getQuery('lastRegisration')->getField('users', 'registration@reg_interval')->addModifier('max');
    $this->osuql->addUnion('main', '@firstRegisration union @lastRegisration');
    $this->assertEquals($this->osuql->getSQL(['main']),
      '(select min(users.registration) as reg_interval from users) '.
        'union '.
      '(select max(users.registration) as reg_interval from users)'
    );
    $this->assertNull($this->osuql->getSQL('all'));
  }

  public function testEmptyDriver(): void
  {
    $this->osuql = new SuQLObject;
    $this->osuql->addSelect('main');
    $this->osuql->getQuery('main')->addFrom('users');
    $this->assertFalse($this->osuql->getSQL(['main']));
    $this->assertEquals($this->osuql->getLog(), [
      'error' => [SuQLError::DRIVER_NOT_DEFINED],
    ]);
  }
}
