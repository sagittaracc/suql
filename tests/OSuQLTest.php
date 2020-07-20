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

  public function testSelectWhere(): void
  {
    $this->init();

    $this->db->select()
                ->users()
                  ->field(['id' => 'uid'])
                  ->field(['name' => 'uname'])
                ->where('uid % 2 = 0');

    $this->assertEquals($this->db->getSQL(), 'select users.id as uid, users.name as uname from users where users.id % 2 = 0');
    $this->assertNull($this->db->getSQL());

    $this->db->query('users_belong_to_any_group')
                ->select()
                  ->user_group('distinct')
                    ->field('user_id');
    $this->db->query()
              ->select()
                ->users()
                  ->field('id@uid')
                  ->field('name')
                ->where('uid not in @users_belong_to_any_group');

    $this->db->assertEquals($this->db->getSQL(), 'select users.id as uid, users.name from users where users.id not in (select distinct user_group.user_id from user_group)');
    $this->db->assertNull($this->db->getSQL());
  }
}
