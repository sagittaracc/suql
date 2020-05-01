<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;

final class MySQLTest extends TestCase
{
  public function testOne(): void
  {
    $db = (new SuQL())->setAdapter('mysql');

    $suql = "
      @allUsers = SELECT FROM users
        id,
        name
      WHERE id > 2
      OFFSET 0
      LIMIT 3;

      @allGroups = SELECT FROM groups
        id@gid
      ;

      SELECT FROM @allUsers
        id
      WHERE id > 10;
    ";

    $this->assertEquals(
      true,
      $db->query($suql)->parse()
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
