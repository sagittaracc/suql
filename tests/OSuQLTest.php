<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;

final class OSuQLTest extends TestCase
{
  private $db = null;

  private function InitDB() {
    $this->db = (new OSuQL)->rel(['users' => 'a'], ['user_group' => 'b'], 'a.id = b.user_id')
                           ->rel(['user_group' => 'a'], ['groups' => 'b'], 'a.group_id = b.id');

    $this->db->setAdapter('mysql');
  }

  public function testSelect(): void
  {
    $this->initDB();

    // Getting the all fields
    $this->assertEquals(
      "select users.* from users",
      $this->db->select()
                ->users()
                  ->field('*')
               ->getSQL()
    );

    // Getting the same sql again should return an empty result
    $this->assertNull($this->db->getSQL());
    $this->assertNull($this->db->getSQLObject());

    // Getting specific fields
    $this->assertEquals(
      "select users.id, users.name from users",
      $this->db->select()
                ->users()
                  ->field('id')
                  ->field('name')
               ->getSQL()
    );

    // Using aliases
    $this->assertEquals(
      "select users.id as uid, users.name as uname from users",
      $this->db->select()
                ->users()
                  ->field(['id' => 'uid'])
                  ->field(['name' => 'uname'])
               ->getSQL()
    );
  }

  public function testSelectDistinct(): void
  {
    $db = (new OSuQL)->setAdapter('mysql');

    $osuql = $db->select()
                  ->users('distinct')
                    ->field('id');

    $this->assertEquals(
      "select distinct users.id from users",
      $db->getSQL()
    );

    $db->drop();
  }

  public function testJoinChain(): void
  {
    $db = (new OSuQL)->setAdapter('mysql')
                     ->rel('table1', 'table2', 'table1.t1id = table2.t2id')
                     ->rel('table1', 'table3', 'table1.t1id = table3.t3id')
                     ->rel('table2', 'table4', 'table2.t2id = table4.t4id')
                     ->rel('table3', 'table5', 'table3.t3id = table5.t5id')
                     ->rel('table1', 'table6', 'table1.t1id = table6.t6id');

    $osuql = $db->select()
                  ->table1()
                  ->table2()
                  ->table3()
                  ->table4()
                  ->table5()
                  ->table6()
                ->getSQLObject();

    $this->assertEquals(
      [
        'config' => [
          'var_declare' => '@',
        ],
        'queries' => [
          'main' => [
            'type'     => 'select',
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
            'table_list' => ['table1', 'table2', 'table3', 'table4', 'table5', 'table6'],
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
                     ->rel('table1', 'table3', 'table1.t1id = table3.t3id')
                     ->temp_rel('table4', 'view1', 'table4.t4id = view1.v_id');

    $db->query('view1')
        ->select()
          ->table1()
          ->table2()
          ->table3()
       ->query()
        ->select()
          ->table4()
          ->view1();

    $this->assertEquals(
      [
        'config' => [
          'var_declare' => '@',
        ],
        'queries' => [
          'view1' => [
            'type'     => 'select',
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
            'table_list' => ['table1', 'table2', 'table3'],
          ],
          'main' => [
            'type'     => 'select',
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
            'table_list' => ['table4', 'view1'],
          ]
        ]
      ],
      $db->getSQLObject()
    );
  }

  public function testWhere(): void
  {
    $db = new OSuQL;

    $db->select()
        ->users()
          ->field(['id' => 'uid'])
          ->field(['name' => 'uname'])
        ->where("uid > 5 and uname = 'admin'");

    $this->assertEquals(
      "select users.id as uid, users.name as uname from users where users.id > 5 and users.name = 'admin'",
      $db->setAdapter('mysql')->getSQL()
    );
  }

  public function testUnion(): void
  {
    $db = new OSuQL;
    $db = $db->setAdapter('mysql');
    $db->query('q1')    // Query q1
         ->select()
           ->users()
             ->field('*')
       ->query('q2')    // Query q2
         ->select()
           ->groups()
             ->field('*')
       ->query('q3')    // Query q3
         ->select()
           ->user_group()
             ->field('*')
       ->query('q4')    // Query q4
         ->union('q1')
         ->unionAll('q2')
         ->union('q3')
       ->select()        // Main query
         ->q4()
           ->field('*');

    $this->assertEquals(
      'select q4.* from ('.
        '(select users.* from users) '.
        'union all '.
        '(select groups.* from groups) '.
        'union '.
        '(select user_group.* from user_group)'.
      ') q4',
      $db->getSQL()
    );
  }
}
