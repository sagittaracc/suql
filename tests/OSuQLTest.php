<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;

final class OSuQLTest extends TestCase
{
  public function testQuery(): void
  {
    $db = (new OSuQL)->setAdapter('mysql')
                     ->rel('table1', 'table2', 'id = t2id')
                     ->rel('table1', 'table3', 'id = t3id');

    $osuql = $db->query()
                  ->table1()
                  ->table2()
                  ->table3()
                ->getSQLObject();

    $this->assertEquals(
      [
        'queries' => [
          'main' => [
            'select'   => [],
      			'from'     => 'table1',
      			'where'    => [],
      			'having'   => [],
      			'join'     => [
              'table2' => ['table' => 'table2', 'on' => 'table1.id = table2.t2id'],
              'table3' => ['table' => 'table3', 'on' => 'table1.id = table3.t3id'],
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
  }
}
