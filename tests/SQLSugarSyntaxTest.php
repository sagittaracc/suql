<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;

final class SQLSugarSyntaxTest extends TestCase
{
  public function testSelect(): void
  {
    $db = new SQLSugarSyntax;

    $db->rel(['users' => 'a'], ['user_group' => 'b'], 'a.id = b.user_id');
    $db->rel(['user_group' => 'a'], ['groups' => 'b'], 'a.group_id = b.id');

    $db->addQuery('main');
    $db->addFrom('main', 'users');
    $db->addJoin('main', 'inner', 'user_group');
    $db->addJoin('main', 'inner', 'groups');
    $db->addField('main', 'groups', ['name' => 'gname']);
    $db->addField('main', 'groups', ['name' => 'count']);
    $db->addModifier('main', 'count', 'group', []);
    $db->addModifier('main', 'count', 'count', []);

    $db->setAdapter('mysql');

    $this->assertEquals(
      'select '.
        'groups.name as gname, '.
        'count(groups.name) as count '.
      'from users '.
      'inner join user_group on users.id = user_group.user_id '.
      'inner join groups on user_group.group_id = groups.id '.
      'group by groups.name',
      $db->getSQL()
    );

    $this->assertEquals(
      [],
      $db->getSQLObject()
    );
  }
}
