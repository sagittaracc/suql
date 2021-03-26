<?php declare(strict_types = 1);
use core\SuQLSpecialSymbols;
use PHPUnit\Framework\TestCase;

final class NewSuQLTest extends TestCase
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

  public function testSelect(): void
  {
    $this->init();

    $this->assertEquals(
      $this->suql->query('
        select
          users {
            id:uid,
            name
          }
          > user_group {

          }
          < groups {
            name,
            name.group.count:count
          }
        ;
      ')->getSQL(),
      'select '.
        'users.id as uid, users.name, groups.name, count(groups.name) as count '.
      'from users '.
      'right join user_group on users.id = user_group.user_id '.
      'left join groups on user_group.group_id = groups.id '.
      'group by groups.name'
    );
    $this->assertNull($this->suql->getSQL());
  }

  public function testSome(): void
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
    'select '.
      'round(clients.lat, 4) as lat, '.
      'round(clients.lon, 4) as lon, '.
      'count(clients.id) as count, '.
      'group_concat(clients.id separator \':\') as listId '.
    'from clients '.
    'group by clients.lat, clients.lon '.
    'having lat <> \'0.0000\' '.
      'and lon <> \'0.0000\' '.
      'and count > 1 '.
    'order by count asc'
    );
  }
}
