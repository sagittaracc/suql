<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;

final class SuQLTest extends TestCase
{
  private $db = null;

  private function InitDB() {
    $this->db = (new SuQL)->rel(['users' => 'a'], ['user_group' => 'b'], 'a.id = b.user_id')
                           ->rel(['user_group' => 'a'], ['groups' => 'b'], 'a.group_id = b.id');

    $this->db->setAdapter('mysql');
  }

  public function testSelect(): void
  {
    $this->initDB();

    $this->assertEquals(
      'select users.* from users',
      $this->db->query("
        SELECT FROM users
          *
        ;
      ")->getSQL()
    );

    $this->assertNull($this->db->getSQL());
    $this->assertEmpty($this->db->getSQLObject());

    $this->assertEquals(
      'select users.id, users.name from users',
      $this->db->query("
        SELECT FROM users
          id,
          name
        ;
      ")->getSQL()
    );

    $this->assertEquals(
      'select users.id as uid, users.name as uname from users',
      $this->db->query("
        SELECT FROM users
          id@uid,
          name@uname
        ;
      ")->getSQL()
    );
  }

  public function testSelectDistinct(): void
  {
    $db = (new SuQL)->setAdapter('mysql');

    $this->assertEquals(
      'select distinct users.id from users',
      $db->query("
        SELECT DISTINCT FROM users
          id
        ;
      ")->getSQL()
    );
  }

  public function testSelectFields(): void
  {
    $db = (new SuQL)->setAdapter('mysql');

    $this->assertEquals(
      'select users.id as uid, users.name as uname from users',
      $db->query("
        SELECT FROM users
          id@uid,
          name@uname
        ;
      ")->getSQL()
    );
  }

  public function testSelectWhere(): void
  {
    $db = (new SuQL)->setAdapter('mysql');

    $this->assertEquals(
      "select users.id as uid, users.name as uname from users where users.id > 5 and users.name <> 'admin'",
      $db->query("
        SELECT FROM users
          id@uid,
          name@uname
        WHERE uid > 5 and uname <> 'admin'
        ;
      ")->getSQL()
    );
  }

  public function testJoinGroup(): void
  {
    $db = (new SuQL())->setAdapter('mysql');

    $db->rel(['users' => 'a'], ['user_group' => 'b'], 'a.id = b.user_id');
    $db->rel(['user_group' => 'a'], ['groups' => 'b'], 'a.group_id = b.id');

    $this->assertEquals(
      "select allUsers.gname, allUsers.cnt ".
      "from (".
        "select ".
          "groups.name as gname, ".
          "count(groups.name) as cnt ".
        "from users ".
        "inner join user_group on users.id = user_group.user_id ".
        "inner join groups on user_group.group_id = groups.id ".
        "group by groups.name".
      ") allUsers ".
      "where gname = 'admin'"
      ,
      $db->query("
        @allUsers = SELECT FROM users
                    INNER JOIN user_group
                    INNER JOIN groups
                      name@gname
                      name.group.count@cnt
                    ;

        SELECT FROM @allUsers
          gname,
          cnt
        WHERE gname = 'admin'
        ;
      ")->getSQL()
    );
  }

  public function testUnion1(): void
  {
    $query = '
      @q3 = @q1 union all @q2 union @q4 ;
      select from @q3 * ;
    ';

    $queryList = SuQLParser::getQueryList($query);

    $this->assertEquals([
        'q3'   => '@q1 union all @q2 union @q4 ;',
        'main' => 'select from @q3 * ;'
    ], $queryList);

    $queryTypes = [];
    foreach ($queryList as $_name => $_query) {
      $queryTypes[$_name] = SuQLParser::getQueryHandler($_query);
    }

    $this->assertEquals([
      'q3'   => 'UNION',
      'main' => 'SELECT',
    ], $queryTypes);

    $db = (new SuQL)->setAdapter('mysql');
    $osuql = $db->query($query)->getSQLObject();

    $this->assertEquals([
      'queries' => [
        'main' => [
          'type'       => 'select',
          'select'     => [
            'q3.*' => [
              'table' => 'q3',
              'field' => 'q3.*',
              'alias' => '',
              'visible' => true,
              'modifier' => []
            ]
          ],
          'from'       => 'q3',
          'where'      => [],
          'having'     => [],
          'join'       => [],
          'group'      => [],
          'order'      => [],
          'modifier'   => null,
          'offset'     => null,
          'limit'      => null,
          'table_list' => ['q3'],
        ],
        'q3' => [
          'type' => 'union',
          'suql' => '@q1 union all @q2 union @q4',
        ]
      ]
    ], $osuql);
  }

  public function testUnion2(): void
  {
    $query = '
      @q1 = select from users * ;
      @q2 = select from groups * ;
      @q3 = select from user_group * ;
      @q4 = @q1 union all @q2 union @q3 ;
      select from @q4 * ;
    ';

    $db = new SuQL;
    $db = $db->setAdapter('mysql');
    $db->query($query);

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

  public function testSuQLSpecialSymbols(): void
  {
    $old = SuQLRegExp::$prefix_declare_variable;
    SuQLRegExp::$prefix_declare_variable = ['#'];

    $query = '
      #q3 = #q1 union all #q2 union #q4 ;
      select from #q3 * ;
    ';

    $db = (new SuQL)->setAdapter('mysql');
    $osuql = $db->query($query)->getSQLObject();

    $this->assertEquals([
      'queries' => [
        'main' => [
          'type'       => 'select',
          'select'     => [
            'q3.*' => [
              'table' => 'q3',
              'field' => 'q3.*',
              'alias' => '',
              'visible' => true,
              'modifier' => []
            ]
          ],
          'from'       => 'q3',
          'where'      => [],
          'having'     => [],
          'join'       => [],
          'group'      => [],
          'order'      => [],
          'modifier'   => null,
          'offset'     => null,
          'limit'      => null,
          'table_list' => ['q3'],
        ],
        'q3' => [
          'type' => 'union',
          'suql' => '#q1 union all #q2 union #q4',
        ]
      ]
    ], $osuql);

    SuQLRegExp::$prefix_declare_variable = $old;
  }

  public function testAfterChangingSuQLSpecialSymbols(): void
  {
    $query = '
      @q1 = select from users * ;
      @q2 = select from groups * ;
      @q3 = select from user_group * ;
      @q4 = @q1 union all @q2 union @q3 ;
      select from @q4 * ;
    ';

    $db = new SuQL;
    $db = $db->setAdapter('mysql');
    $db->query($query);

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
