<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;

final class OSuQLTest extends TestCase
{
  public function testJoinChain(): void
  {
    $db = (new OSuQL)->setAdapter('mysql')
                     ->rel('table1', 'table2', 'table1.t1id = table2.t2id')
                     ->rel('table1', 'table3', 'table1.t1id = table3.t3id')
                     ->rel('table2', 'table4', 'table2.t2id = table4.t4id')
                     ->rel('table3', 'table5', 'table3.t3id = table5.t5id')
                     ->rel('table1', 'table6', 'table1.t1id = table6.t6id');

    $osuql = $db->query()
                  ->table1()
                  ->table2()
                  ->table3()
                  ->table4()
                  ->table5()
                  ->table6()
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
              'table2' => ['table' => 'table2', 'on' => 'table1.t1id = table2.t2id', 'type' => 'inner'],
              'table3' => ['table' => 'table3', 'on' => 'table1.t1id = table3.t3id', 'type' => 'inner'],
              'table4' => ['table' => 'table4', 'on' => 'table2.t2id = table4.t4id', 'type' => 'inner'],
              'table5' => ['table' => 'table5', 'on' => 'table3.t3id = table5.t5id', 'type' => 'inner'],
              'table6' => ['table' => 'table6', 'on' => 'table1.t1id = table6.t6id', 'type' => 'inner'],
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

  public function testTempRel(): void
  {
    $db = (new OSuQL)->setAdapter('mysql')
                     ->rel('table1', 'table2', 'table1.t1id = table2.t2id and table1.lid = table2.rid')
                     ->rel('table1', 'table3', 'table1.t1id = table3.t3id');

    $db->query('view1')
        ->table1()
        ->table2()
        ->table3();

    $db->temp_rel('table4', 'view1', 'table4.t4id = view1.v_id');

    $db->query()
        ->table4()
        ->view1();

    $this->assertEquals(
      [
        'queries' => [
          'view1' => [
            'select'   => [],
      			'from'     => 'table1',
      			'where'    => [],
      			'having'   => [],
      			'join'     => [
              'table2' => ['table' => 'table2', 'on' => 'table1.t1id = table2.t2id and table1.lid = table2.rid', 'type' => 'inner'],
              'table3' => ['table' => 'table3', 'on' => 'table1.t1id = table3.t3id', 'type' => 'inner'],
            ],
      			'group'    => [],
      			'order'    => [],
      			'modifier' => null,
      			'offset'   => null,
      			'limit'    => null,
          ],
          'main' => [
            'select'   => [],
      			'from'     => 'table4',
      			'where'    => [],
      			'having'   => [],
      			'join'     => [
              'view1' => ['table' => 'view1', 'on' => 'table4.t4id = view1.v_id', 'type' => 'inner'],
            ],
      			'group'    => [],
      			'order'    => [],
      			'modifier' => null,
      			'offset'   => null,
      			'limit'    => null,
          ]
        ]
      ],
      $db->getSQLObject()
    );
  }
}
