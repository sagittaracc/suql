<?php declare(strict_types = 1);
use core\SuQLSpecialSymbols;
use PHPUnit\Framework\TestCase;

final class SuQLTest extends TestCase
{
  private $suql = null;

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

  public function testCustomWhere(): void
  {
    $this->init();

    $this->assertEquals(
      $this->suql->query("
        select
          users {
            id.where('$ mod 2 = 0')
          }
        ;
      ")->getSQL(),
      "select users.id from users where users.id mod 2 = 0"
    );
  }

  public function testQueryNestedInWhere(): void
  {
    $this->init();

    $this->assertEquals(
      $this->suql->query("
        @q1 = select users {};

        select
          groups {
            name.where($ not in @q1)
          }
        ;
      ")->getSQL(),
      "select groups.name from groups where groups.name not in (select * from users)"
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

  public function testCase(): void
  {
    $this->init();

    $this->assertEquals(
      $this->suql->query('
        select
          users {
            role.role:caption
          }
        ;
      ')->getSQL(),
      "select ".
        "case ".
          "when users.role = 1 then 'admin' ".
          "when users.role = 2 then 'user' ".
          "when users.role = 3 then 'guest' ".
          "else '' end as caption ".
      "from users"
    );
  }

  public function testIfFunction(): void
  {
    $this->init();

    $this->assertEquals($this->suql->query("
      select
        users {
          id,
          role.ifNull('no role', 'role'),
          role.ifZero('no role', 'role'):zeroRole
        }
      ;
    ")->getSQL(),
    "select ".
      "users.id, ".
      "if(users.role is null, 'no role', 'role'), ".
      "if(users.role = 0, 'no role', 'role') as zeroRole ".
    "from users"
    );
  }

  public function testFunctionInCaseClause(): void
  {
    $this->init();

    $this->assertEquals($this->suql->query("
      select
        users {
          id.mod(2).even:isEven
        }
      ;
    ")->getSQL(),
    "select case when mod(users.id, 2) = 1 then 'no' when mod(users.id, 2) = 0 then 'yes' end as isEven from users"
    );
  }

  public function testArithmetic(): void
  {
    $this->init();

    $this->assertEquals($this->suql->query("
      select
        users {
          id.div(2)
        }
      ;
    ")->getSQL(),
    "select users.id / 2 from users"
    );
  }

  public function testUseNowSQLSpecialWord(): void
  {
    $this->init();

    $this->assertEquals($this->suql->query("
      select
        users {
          register.datediffnow()
        }
      ;
    ")->getSQL(),
    "select datediff(users.register, now()) ".
    "from users"
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

  public function testAnotherComplicatedQuery(): void
  {
    $this->init();

    // TODO: underscore needs because counter ends with an 'r' and resurs starts with an 'r'. It leads to incorrect replacements
    $this->suql->rel(['counter' => 'c_'], ['resurs' => 'r_'], 'r_.id_Resurs = c_.Id_Resurs');
    $this->suql->rel(['counter' => 'c_'], ['users' => 'u_'], 'u_.Obj_Id_User = c_.Obj_Id_User');

    $this->assertEquals($this->suql->query("
      select
        counter {
          AI_Counter:id,
          Obj_Id_User,
          Obj_Id_Counter:counter_id,
          Name.concat(' [', @SerialNumber, ']'):caption,
          State:state,
        }

        resurs {
          Name_Resurs:resurs,
          Unit:unit
        }

        users {

        }
      ;
    ")->getSQL(),
    "select ".
    	"counter.AI_Counter as id, ".
    	"counter.Obj_Id_User, ".
    	"counter.Obj_Id_Counter as counter_id, ".
      "concat(counter.Name, ' [', counter.SerialNumber, ']') as caption, ".
      "counter.State as state, ".
    	"resurs.Name_Resurs as resurs, ".
    	"resurs.Unit as unit ".
    "from counter ".
    "inner join resurs on resurs.id_Resurs = counter.Id_Resurs ".
    "inner join users on users.Obj_Id_User = counter.Obj_Id_User"
    );
  }
}
