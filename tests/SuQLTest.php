<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;

final class SuQLTest extends TestCase
{
  public function testSelect(): void
  {
    $this->assertEquals(
      'select users.* from users',
      SuQL::fromString('users {*};')
    );
  }

  public function testSelectFields(): void
  {
    $this->assertEquals(
      'select users.id as uid, users.name as uname from users',
      SuQL::fromString('users {id@uid, name@uname};')
    );
  }
}
