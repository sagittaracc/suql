<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;

final class SuQLTest extends TestCase
{
  public function testOne(): void
  {
    $db = (new SuQL())->setAdapter('mysql');

    $db->rel(['users' => 'a'], ['user_group' => 'b'], 'a.id = b.user_id');
    $db->rel(['user_group' => 'a'], ['groups' => 'b'], 'a.group_id = b.id');

    $suql = "
      @allUsers = SELECT FROM users
        id,
        name

      LEFT JOIN user_group
        user_id,
        group_id

      RIGHT JOIN groups
        id,
        name

      OFFSET 0
      LIMIT 3;

      SELECT FROM @allUsers
        id
      WHERE id > 10;
    ";

    $this->assertEquals(
      [
        'queries' => [
          'main' => [
            'select'   => [],
            'from'     => 'allUsers',
            'where'    => ['id > 10'],
            'having'   => [],
            'join'     => [],
            'group'    => [],
            'order'    => [],
            'modifier' => null,
            'offset'   => null,
            'limit'    => null,
          ],
          'allUsers' => [
            'select'   => [],
            'from'     => 'users',
            'where'    => [],
            'having'   => [],
            'join'     => [
              'user_group' => ['table' => 'user_group', 'type' => 'LEFT', 'on' => 'users.id = user_group.user_id'],
              'groups' => ['table' => 'groups', 'type' => 'RIGHT', 'on' => 'user_group.group_id = groups.id'],
            ],
            'group'    => [],
            'order'    => [],
            'modifier' => null,
            'offset'   => '0',
            'limit'    => '3',
          ],
        ]
      ],
      $db->query($suql)->getSQLObject()
    );
  }
}

/*
@allusers = select from users
	id,
	name
where id > 1

left join user_group
	user_id,
	group_id
where user_id > 3 and group_id in @someview

inner join groups
	name@gname,
	name.group.count@count
where @count = 2 and name = 'admin';

select from @allusers
	id,
	name
;
*/
