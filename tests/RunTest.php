<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;

final class RunTest extends TestCase
{
  private $suql;

  private function init()
  {
    $this->suql = new SuQL;

    $this->suql->rel(['users' => 'u'], ['user_group' => 'ug'], 'u.id = ug.user_id');
    $this->suql->rel(['user_group' => 'ug'], ['groups' => 'g'], 'ug.group_id = g.id');

    $this->suql->setAdapter('mysql');

    error_reporting(E_ALL ^ E_WARNING);
    include 'external/Db.php';
    error_reporting(E_ALL);

    if (class_exists('Db'))
      $this->suql->setDb(new Db('localhost', 'root', '', 'ug'));
  }

  public function testSelect(): void
  {
    $this->init();

    if ($this->suql->getDb()) {
      $userList = $this->suql->query('
        SELECT FROM users
          id,
          name
        WHERE id > ?;
      ')->run([3]);

      $this->assertEquals($userList, [
        0 => ['id' => 4, 'name' => 'Den'],
        1 => ['id' => 5, 'name' => 'Feodol'],
      ]);
    }
    else
      $this->assertNull($this->suql->exec('main'));
  }
}
