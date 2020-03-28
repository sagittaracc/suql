<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;

final class SuQLTest extends TestCase
{
  public function testSimpleSelect(): void
  {
    $this->assertEquals(
      str_replace(["\r\n", "\t", ' '], '', 'select users.* from users'),
      str_replace(["\r\n", "\t", ' '], '', (new SuQL('users {*};'))->pureSQL())
    );
  }
}
