<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;

final class OSuQLTest extends TestCase
{
  public function testQuery(): void
  {
    $db = new OSuQL;
    $osuql = $db->query()
                  ->users()
                    ->field('id', 'uid')->group()->count()
                    ->field('name')
                  ->where('uid % 2 = 0')
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
                'modifier' => [
                  'group' => [],
                  'count' => [],
                ],
              ],
              'users.name' => [
                'table' => 'users',
                'field' => 'name',
                'alias' => '',
                'modifier' => [],
              ]
            ],
      			'from'     => 'users',
      			'where'    => ['uid % 2 = 0'],
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
