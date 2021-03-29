<?php declare(strict_types = 1);
use core\SuQLSpecialSymbols;
use PHPUnit\Framework\TestCase;

final class SuQLTest extends TestCase
{
  private $suql;

  private function init()
  {
    $this->suql = new SuQL;

    // Init the database scheme
    $this->suql->rel(['users' => 'u'], ['user_group' => 'ug'], 'u.id = ug.user_id');
    $this->suql->rel(['user_group' => 'ug'], ['groups' => 'g'], 'ug.group_id = g.id');

    // Setting DBMS
    $this->suql->setAdapter('mysql');
  }

  public function testSelectAll(): void
  {
    $this->init();

    $this->assertEquals(
      $this->suql->query('
        select users {};
      ')->getSQL(),
      'select * from users'
    );
  }

  public function testSelectFields(): void
  {
    $this->init();

    $this->assertEquals(
      $this->suql->query('
        select
          users {
            id,
            name
          }
        ;
      ')->getSQL(),
      'select users.id, users.name from users'
    );
  }

  public function testSelectFieldsWithAliases(): void
  {
    $this->init();

    $this->assertEquals(
      $this->suql->query('
        select
          users {
            id:uid,
            name:uname
          }
        ;
      ')->getSQL(),
      'select users.id as uid, users.name as uname from users'
    );
  }

  public function testSelectWhere(): void
  {
    $this->init();

    $this->assertEquals(
      $this->suql->query("
        select
          users {
            id,
            name.like('admin')
          }
        ;
      ")->getSQL(),
      "select users.id, users.name from users where users.name like '%admin%'"
    );
  }

  public function testJoin(): void
  {
    $this->init();

    $this->assertEquals(
      $this->suql->query('
        select
          users {}
          user_group {}
          < groups {
            name,
            name.group.count:count
          }
        ;
      ')->getSQL(),
      'select '.
        'groups.name, count(groups.name) as count '.
      'from users '.
      'inner join user_group on users.id = user_group.user_id '.
      'left join groups on user_group.group_id = groups.id '.
      'group by groups.name'
    );
    $this->assertNull($this->suql->getSQL());
  }

  public function testNestedQuery(): void
  {
    $this->init();

    $this->assertEquals(
      $this->suql->query('
        @userCount = select
                      users {}
                      user_group {}
                      > groups {
                        name,
                        name.group.count:count
                      }
        ;

        select
          userCount {
            name,
            count.less(3)
          }
        ;
      ')->getSQL(),
      'select userCount.name, userCount.count '.
      'from ('.
        'select groups.name, count(groups.name) as count '.
        'from users '.
        'inner join user_group on users.id = user_group.user_id '.
        'right join groups on user_group.group_id = groups.id '.
        'group by groups.name'
      .') userCount '.
      'where userCount.count < 3'
    );
  }

  public function testUnionQuery(): void
  {
    $this->init();

    $this->assertEquals(
      $this->suql->query('
        @q1 = select users {*};
        @q2 = select groups {*};
        @q3 = @q1 union @q2;
      ')->getSQL(['q3']),
      '(select users.* from users) union (select groups.* from groups)'
    );
  }

  public function testComplicatedQuery(): void
  {
    $this->init();

    $this->assertEquals($this->suql->query("
      select
        clients {
          lat.round(4).notEqual('0.0000').group:lat,
          lon.round(4).notEqual('0.0000').group:lon,
          id.count.greater(1).asc:count,
          id.implode(':'):listId
        }
      ;
    ")->getSQL(),
    "select ".
      "round(clients.lat, 4) as lat, ".
      "round(clients.lon, 4) as lon, ".
      "count(clients.id) as count, ".
      "group_concat(clients.id separator ':') as listId ".
    "from clients ".
    "group by clients.lat, clients.lon ".
    "having lat <> '0.0000' ".
      "and lon <> '0.0000' ".
      "and count > 1 ".
    "order by count asc"
    );
  }
}
