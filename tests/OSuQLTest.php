<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;

final class OSuQLTest extends TestCase
{
  private $db;

  private function init()
  {
    $this->db = new OSuQL;

    $this->db->rel(['users' => 'u'], ['user_group' => 'ug'], 'u.id = ug.user_id');
    $this->db->rel(['user_group' => 'ug'], ['groups' => 'g'], 'ug.group_id = g.id');

    $this->db->setAdapter('mysql');
  }

  public function testSelect(): void
  {
    $this->init();

    $this->db->select()
                ->users()
                  ->field('id')
                  ->field('name');

    $this->assertEquals($this->db->getSQL(), 'select users.id, users.name from users');
    $this->assertNull($this->db->getSQL());

    $this->db->select()
                ->users()
                  ->field('*');

    $this->assertEquals($this->db->getSQL(), 'select users.* from users');
    $this->assertNull($this->db->getSQL());

    $this->db->select()
                ->users();

    $this->assertEquals($this->db->getSQL(), 'select * from users');
    $this->assertNull($this->db->getSQL());

    $this->db->select()
                ->users()
                  ->field(['id' => 'uid'])
                  ->field('name@uname');

    $this->assertEquals($this->db->getSQL(), 'select users.id as uid, users.name as uname from users');
    $this->assertNull($this->db->getSQL());
  }
}
