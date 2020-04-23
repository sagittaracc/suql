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
                    ->field('id')
                  ->user_group()
                  ->groups()
                    ->field('name', 'gname')
                  ->where("gname = 'admin'")
                ->getSQLObject();

    $this->assertEquals(
      [
        'queries' => [
          'main' => [
            'select'   => [
              'users.id' => [
                'table' => 'users',
                'field' => 'id',
                'alias' => '',
                'modifier' => [],
              ],
              'gname' => [
                'table' => 'groups',
                'field' => 'name',
                'alias' => 'gname',
                'modifier' => [],
              ],
            ],
      			'from'     => 'users',
      			'where'    => ["gname = 'admin'"],
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

    $db->drop();

    $osuql = $db->query()
                  ->users()
                  ->user_group();

    $this->assertEquals(null, $osuql);
  }
}
