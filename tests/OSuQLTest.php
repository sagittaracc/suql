<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;

final class OSuQLTest extends TestCase
{
  public function testQuery(): void
  {
    $db = (new OSuQL)->rel('users', 'user_group', 'id = user_id')
                     ->rel('user_group', 'groups', 'group_id = id');

    $osuql = $db->query()
                  ->users()
                    ->field('id', 'uid')
                    ->field('name')
                  ->user_group()
                  ->groups()
                ->getSQLObject();

    $this->assertEquals(
      [
        'queries' => [
          'main' => [
            'select'   => [
              'uid' => [
                'table' => 'users',
                'field' => 'id',
                'alias' => 'uid',
                'modifier' => [],
              ],
              'users.name' => [
                'table' => 'users',
                'field' => 'name',
                'alias' => '',
                'modifier' => [],
              ]
            ],
      			'from'     => 'users',
      			'where'    => [],
      			'having'   => [],
      			'join'     => [
              'user_group' => ['table' => 'user_group', 'on' => 'users.id = user_group.user_id'],
              'groups' => ['table' => 'groups', 'on' => 'user_group.group_id = groups.id'],
            ],
      			'group'    => [],
      			'order'    => [],
      			'modifier' => null,
      			'offset'   => null,
      			'limit'    => null,
          ]
        ]
      ],
      $osuql
    );

    $this->assertEquals([], $db->getSQLObject());
  }
}
