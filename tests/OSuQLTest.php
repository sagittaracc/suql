<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;

final class OSuQLTest extends TestCase
{
  public function testQuery(): void
  {
    $db = new OSuQL;
    $osuql = $db->query()
                  ->users()
                  ->field('id', 'uid')
                  ->field('name')
                ->getSQLObject();

    $this->assertEquals(
      [
        'queries' => [
          'main' => [
            'select'   => [
              'uid' => ['table' => 'users', 'field' => 'id', 'alias' => 'uid'],
              'users.name' => ['table' => 'users', 'field' => 'name', 'alias' => '']
            ],
      			'from'     => 'users',
      			'where'    => [],
      			'having'   => [],
      			'join'     => [],
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
