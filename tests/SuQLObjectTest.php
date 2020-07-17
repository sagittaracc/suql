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
    $this->assertTrue($db->hasRel('user_group', 'users'));
    $this->assertFalse($db->hasRel('users', 'groups'));
    $this->assertEquals($db->getRelType('groups', 'user_group'), 'rel');
    $this->assertEquals($db->getRel('groups', 'user_group'), 'user_group.group_id = groups.id');
    $this->assertFalse($db->hasRel('users', 'groups'));
    $this->assertNull($db->getRel('users', 'groups'));

    $db->addSelect('main');
    $this->assertTrue($db->hasQuery('main'));
    $this->assertFalse($db->hasQuery('some_query'));

    $db->getQuery('main')->addField('users', 'id');
    $this->assertTrue($db->getQuery('main')->hasField('users.id'));
  }
}
