<?php
class SuQLParser
{
  const REGEX_DETECT_SELECT_QUERY_TYPE = '/^select.*?;$/msi';
  const REGEX_DETECT_UNION_QUERY_TYPE = '/^({:v:}\w+\s+union\s+(all\s+)?)+{:v:}\w+\s*;/msi';

  // <var_name> = <query>;
  const REGEX_NESTED_QUERY = '/{:v:}(?<name>\w+)\s*=\s*(?<query>.*?;)/msi';
  const REGEX_MAIN_SELECT = '/^;?\s*(select.*?;)/msi';
  /*
   *    select from <table>
   *        <field list>
   *    [where <conditions>]
   *    [
   *        (left|right|inner) join <table>
   *            <field list>
   *        [where <conditions>]
    *    ]
   *    [offset <offset>]
   *    [limit <limit>]
   */
  const REGEX_SELECT = "/(select\s+(?<modif>\w+))?\s+(?<type>from|join)\s+{:v:}?(?<table>\w+)\s+((?<fields>.*?)(where\s+(?<where>.*?)\s*)?)?(?<next>left|right|inner|limit\s+(?<offset>\d+)\s*,\s*(?<limit>\d+)|;)/msi";
  // <field_name[.modif1[(<params>)].modif2.modif3...][field_alias], ...
  const REGEX_FIELDS = '/(?<name>[\*\w]+)(?<modif>.*?)({:f:}(?<alias>\w+))?\s*,?\s*$/msi';
  const REGEX_FIELD_MODIFIERS = '/.(?<name>\w+)(\((?<params>.*?)\))?/msi';

  const REGEX_TRIM_SEMICOLON = '/(.*?);/';

  public static function getQueryHandler($suql) {
    if ((new SuQLRegExp(self::REGEX_DETECT_SELECT_QUERY_TYPE))->match($suql))
      return 'SELECT';
    else if ((new SuQLRegExp(self::REGEX_DETECT_UNION_QUERY_TYPE))->match($suql))
      return 'UNION';
    else
      return null;
  }

  public static function getQueryList($suql) {
    $nested = self::getNestedQueries($suql);
    $main = self::getMainQuery($suql);
    return $main ? array_merge($nested, ['main' => $main]) : $nested;
  }

  public static function getNestedQueries($suql) {
    $nestedQueries = (new SuQLRegExp(self::REGEX_NESTED_QUERY))->match_all($suql);
    return array_combine($nestedQueries['name'], $nestedQueries['query']);
  }

  public static function getMainQuery($suql) {
    return (new SuQLRegExp(self::REGEX_MAIN_SELECT))->match($suql);
  }

  public static function parseSelect($suql) {
    $clauses = (new SuQLRegExp(self::REGEX_SELECT))->match_all($suql);
    array_unshift($clauses['next'], array_pop($clauses['next']));
    $tables = ['tables' => [], 'offset' => null, 'limit' => null];
    for ($i = 0, $n = count($clauses['table']); $i < $n; $i++) {
      $tables['tables'][$clauses['table'][$i]] = [
        'type' => strtolower($clauses['type'][$i]),
        'fields' => $clauses['fields'][$i],
        'where' => $clauses['where'][$i],
        'next' => strtolower($clauses['next'][$i]),
        'modifier' => strtolower($clauses['modif'][$i]),
      ];
    }
    if ($clauses['offset'][count($clauses['offset']) - 1] !== '')
      $tables['offset'] = $clauses['offset'][count($clauses['offset']) - 1];
    if ($clauses['limit'][count($clauses['limit']) - 1] !== '')
      $tables['limit'] = $clauses['limit'][count($clauses['limit']) - 1];
    return $tables;
  }

  public static function getFieldList($suql) {
    return (new SuQLRegExp(self::REGEX_FIELDS))->match_all($suql);
  }

  public static function getFieldModifierList($suql) {
    $fieldModifierList = (new SuQLRegExp(self::REGEX_FIELD_MODIFIERS))->match_all($suql);
    return array_combine($fieldModifierList['name'], $fieldModifierList['params']);
  }

  public static function trimSemicolon($suql) {
    return trim((new SuQLRegExp(self::REGEX_TRIM_SEMICOLON))->match($suql));
  }
}
