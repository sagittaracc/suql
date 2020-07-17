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

    $db->addSelect('main');
    $db->getQuery('main')->addField('users', 'id');
    $this->assertTrue($db->getQuery('main')->hasField('users.id'));
  }
}
