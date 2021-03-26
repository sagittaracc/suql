<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;

final class SuQLRegExpTest extends TestCase
{
  public function testSuQLParser(): void
  {
    $this->assertEquals((new SuQLRegexp(SuQLParser::REGEX_DETECT_SELECT_QUERY_TYPE))->match('select from users *;'), true);
    $this->assertEquals((new SuQLRegexp(SuQLParser::REGEX_DETECT_SELECT_QUERY_TYPE))->match('@q1 union @q2 union @q3;'), false);
    $this->assertEquals(!!(new SuQLRegexp(SuQLParser::REGEX_DETECT_UNION_QUERY_TYPE))->match('@q1 union @q2 union @q3;'), true);
  }
}
