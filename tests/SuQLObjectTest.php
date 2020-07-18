<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;

use core\SuQLObject;

final class SuQLObjectTest extends TestCase
{
  public function testSelect(): void
  {
    $db = new SuQLObject;
    $this->assertEmpty($db->getFullQueryList());

    $db->setAdapter('mysql');
    $this->assertEquals($db->getAdapter(), 'mysql');

    $db->rel(['users' => 'u'], ['user_group' => 'ug'], 'u.id = ug.user_id');
    $db->rel(['user_group' => 'ug'], ['groups' => 'g'], 'ug.group_id = g.id');
    $this->assertTrue($db->hasRelBetween('user_group', 'users'));
    $this->assertFalse($db->hasRelBetween('users', 'groups'));
    $this->assertEquals($db->getRelTypeBetween('groups', 'user_group'), 'rel');
    $this->assertEquals($db->getRelBetween('groups', 'user_group'), 'user_group.group_id = groups.id');
    $this->assertFalse($db->hasRelBetween('users', 'groups'));
    $this->assertNull($db->getRelBetween('users', 'groups'));

    $db->addSelect('main');
    $db->addUnion('another', '@main union @some');
    $this->assertTrue($db->hasQuery('main'));
    $this->assertFalse($db->hasQuery('some_query'));

    $db->getQuery('main')->addField('users', 'id');
    $db->getQuery('main')->getField('users', 'id')->addModifier('max');
    $db->getQuery('main')->addFrom('users');
    $this->assertTrue($db->getQuery('main')->hasField('users', 'id'));
    $this->assertFalse($db->getQuery('main')->hasModifier());
    $this->assertFalse($db->getQuery('main')->getField('users', 'id')->hasAlias());

    $db->getQuery('main')->addJoin('inner', 'user_group');
    $this->assertTrue($db->getQuery('main')->hasJoin('user_group'));
    $this->assertFalse($db->getQuery('main')->hasJoin('groups'));
    $this->assertEquals($db->getQuery('main')->getJoin('user_group')->getType(), 'inner');
    $this->assertEquals($db->getQuery('main')->getJoin('user_group')->getOn(), 'users.id = user_group.user_id');

    $this->assertEquals($db->getSQL('all'), [
      'main' => 'select max(users.id) from users inner join user_group on users.id = user_group.user_id',
      'another' => '(select max(users.id) from users inner join user_group on users.id = user_group.user_id) union ()',
    ]);
  }
}
