<?php
class SuQLParser
{
  const REGEX_DETECT_SELECT_QUERY_TYPE = '/^select.*?;$/msi';
  const REGEX_DETECT_UNION_QUERY_TYPE = '/^({:v:}\w+\s+union\s+(all\s+)?)+{:v:}\w+\s*;/msi';
  const REGEX_DETECT_COMMAND_QUERY_TYPE = '/{:c:}\w+\s+({:v:}\w+,?\s*)*;/msi';

  const REGEX_NESTED_QUERY = '/{:v:}(?<name>\w+)\s*=\s*(?<query>.*?;)/msi';
  const REGEX_MAIN_SELECT = '/^;?\s*(select.*?;)/msi';
  const REGEX_SELECT = "/\n?\s*(?<join>[<>]?)\s*(?<table>\w+)\s*{(?<fields>.*?)}/msi";
  const REGEX_FIELDS = '/(?<name>[\*\w]+)(?<modif>.*?)({:f:}(?<alias>\w+))?\s*,?\s*$/msi';
  const REGEX_FIELD_MODIFIERS = '/.(?<name>\w+)(\((?<params>.*?)\))?/msi';
  const REGEX_COMMAND = '/{:p:}(?<part>\w+)/msi';

  const REGEX_TRIM_SEMICOLON = '/(.*?);/';

  public static function getQueryHandler($suql) {
    if ((new SuQLRegexp(self::REGEX_DETECT_SELECT_QUERY_TYPE))->match($suql))
      return 'SELECT';
    else if ((new SuQLRegexp(self::REGEX_DETECT_UNION_QUERY_TYPE))->match($suql))
      return 'UNION';
    else if ((new SuQLRegexp(self::REGEX_DETECT_COMMAND_QUERY_TYPE))->match($suql))
      return 'COMMAND';
    else
      return null;
  }

  public static function getQueryList($suql) {
    $nested = self::getNestedQueries($suql);
    $main = self::getMainQuery($suql);
    return $main ? array_merge($nested, ['main' => $main]) : $nested;
  }

  public static function getNestedQueries($suql) {
    $nestedQueries = (new SuQLRegexp(self::REGEX_NESTED_QUERY))->match_all($suql);
    return array_combine($nestedQueries['name'], $nestedQueries['query']);
  }

  public static function getMainQuery($suql) {
    return (new SuQLRegexp(self::REGEX_MAIN_SELECT))->match($suql);
  }

  public static function parseSelect($suql) {
    $clauses = (new SuQLRegexp(self::REGEX_SELECT))->match_all($suql);
    $tables = ['tables' => [], 'offset' => null, 'limit' => null];
    for ($i = 0, $n = count($clauses['table']); $i < $n; $i++) {
      $tables['tables'][$clauses['table'][$i]] = [
        'type' => strtolower($clauses['join'][$i]),
        'fields' => $clauses['fields'][$i],
        'where' => '',
        'next' => '',
        'modifier' => '', // Модификатор самого select (например distinct). Теперь будет как модификатор запроса
      ];
    }
    return $tables;
  }

  public static function getFieldList($suql) {
    return (new SuQLRegexp(self::REGEX_FIELDS))->match_all($suql);
  }

  public static function getFieldModifierList($suql) {
    $fieldModifierList = (new SuQLRegexp(self::REGEX_FIELD_MODIFIERS))->match_all($suql);
    return array_combine($fieldModifierList['name'], $fieldModifierList['params']);
  }

  public static function parseCommand($suql) {
    $command = (new SuQLRegexp(self::REGEX_COMMAND))->match_all($suql);
    $command = $command['part'];

    $instruction = array_shift($command);
    $args = $command;

    return [
      'instruction' => $instruction,
      'args' => $args,
    ];
  }
}
