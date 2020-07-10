<?php
class SQLBuilder
{
  const SELECT_TEMPLATE = "#select##from##join##where##group##having##order##limit#";

  private $SQLObject = null;
  private $sql = [];

  function __construct($SQLObject)
  {
    $this->SQLObject = $SQLObject;
  }

  public function getSQLObject()
  {
    return $this->SQLObject;
  }

  public function getSql($queryList)
  {
    if (empty($this->sql)) return null;

    $sqlList = Helper\CArray::slice_by_keys($this->sql, $queryList);

    return count($queryList) === 1 && count($sqlList) === 1
            ? reset($sqlList)
            : $sqlList;
  }

  public function run($queryList)
  {
    if (!$this->SQLObject)
      return;

    foreach ($this->SQLObject['queries'] as $query => $osuql) {
      $this->sql[$query] = trim($this->buildQuery($query));
    }

    foreach ($queryList as $query) {
      $this->sql[$query] = $this->composeQuery($query);
    }
  }

  private function getQuery($query)
  {
    if (isset($this->SQLObject['queries'][$query]))
      return $this->SQLObject['queries'][$query];
    else
      return false;
  }

  private function setQuery($query, $queryObject)
  {
    $this->SQLObject['queries'][$query] = $queryObject;
  }

  private function buildQuery($query)
  {
    $queryObject = $this->getQuery($query);
    $handler = 'build'.ucfirst($queryObject['type']).'Query';
    return method_exists($this, $handler)
            ? $this->$handler($query)
            : null;
  }

  private function buildSelectQuery($query) {
    $selectTemplate = self::SELECT_TEMPLATE;

    $this->setQuery($query, $this->prepareQuery($query));

    $selectTemplate = str_replace('#select#', $this->buildSelect($query), $selectTemplate);
    $selectTemplate = str_replace('#from#'  , $this->buildFrom($query),   $selectTemplate);
    $selectTemplate = str_replace('#join#'  , $this->buildJoin($query),   $selectTemplate);
    $selectTemplate = str_replace('#group#' , $this->buildGroup($query),  $selectTemplate);
    $selectTemplate = str_replace('#where#' , $this->buildWhere($query),  $selectTemplate);
    $selectTemplate = str_replace('#having#', $this->buildHaving($query), $selectTemplate);
    $selectTemplate = str_replace('#order#' , $this->buildOrder($query),  $selectTemplate);
    $selectTemplate = str_replace('#limit#' , $this->buildLimit($query),  $selectTemplate);

    return $selectTemplate;
  }

  private function buildUnionQuery($query) {
    $queryObject = $this->getQuery($query);
    return $queryObject['suql'];
  }

  private function composeQuery($query) {
    if (!isset($this->sql[$query]))
      return '';
    $suql = $this->sql[$query];

    preg_match_all("/@(?<name>\w+)/msi", $suql, $subQueries);
    if (empty($subQueries['name']))
      return $suql;
    else {
      foreach ($subQueries['name'] as $subQuery)
        $suql = str_replace("@$subQuery", '('.$this->composeQuery($subQuery).')', $suql);

      return $suql;
    }
  }

  private function prepareQuery($query) {
    $queryObject = $this->getQuery($query);

    foreach ($queryObject['select'] as $field => $options) {
      if (empty($options['modifier']))
        continue;

      foreach ($options['modifier'] as $modifier => $params) {
        $modifier_handler = "mod_$modifier";
        if (method_exists(SQLModifier::class, $modifier_handler))
          SQLModifier::$modifier_handler($queryObject, $field);
      }
    }

    return $queryObject;
  }

  protected function buildSelect($query) {
    $queryObject = $this->getQuery($query);

    $fields = $queryObject['select'];
    $select = !is_null($queryObject['modifier'])
                ? "select {$queryObject['modifier']} "
                : 'select ';

    if (empty($fields))
      return '';

    $selectList = [];
    foreach ($fields as $fieldOptions) {
      if (isset($fieldOptions['visible']) && $fieldOptions['visible'] === false) continue;
      $selectList[] = $fieldOptions['field'] . ($fieldOptions['alias'] ? " as {$fieldOptions['alias']}" : '');
    }

    return $select . implode(', ', $selectList);
  }

  protected function buildFrom($query) {
    $queryObject = $this->getQuery($query);

    $from = $queryObject['from'];

    if (empty($from))
      return '';

    if ($this->getQuery($from)) {
      return " from @{$from} {$from}";
    } else {
      return " from $from";
    }
  }

  protected function buildJoin($query) {
    $queryObject = $this->getQuery($query);

    $join = $queryObject['join'];
    $select = $queryObject['select'];

    foreach ($join as &$_join) {
      $_join['on'] = str_replace(array_column($select, 'alias'), array_column($select, 'field'), $_join['on']);
    }
    unset($_join);

    if (empty($join))
      return '';

    $s = [];
    foreach ($join as $_join) {
      $table = $_join['table'];
      $table = $this->getQuery($table)
        ? "@$table $table"
        : $table;
      $s[] = "{$_join['type']} join $table on {$_join['on']}";
    }

    return ' ' . implode(' ', $s);
  }

  protected function buildGroup($query) {
    $queryObject = $this->getQuery($query);

    $group = $queryObject['group'];
    return !empty($group) ? ' group by ' . implode(', ', $group) : '';
  }

  protected function buildWhere($query) {
    $queryObject = $this->getQuery($query);

    $where = implode(' and ', $queryObject['where']);
    if (!$where) return '';

    $select = $queryObject['select'];
    $where = str_replace(array_column($select, 'alias'), array_column($select, 'field'), $where);

    return " where $where";
  }

  protected function buildHaving($query) {
    $queryObject = $this->getQuery($query);

    $having = $queryObject['having'];
    return !empty($having) ? ' having ' . implode(' and ', $having) : '';
  }

  protected function buildOrder($query) {
    $queryObject = $this->getQuery($query);

    $order = $queryObject['order'];

    if (empty($order))
      return '';

    $s = [];
    foreach ($order as $_order) {
      $s[] = "{$_order['field']} {$_order['direction']}";
    }

    return ' order by ' . implode(', ', $s);
  }

  protected function buildLimit($query) {
    $bound = [];
    $queryObject = $this->getQuery($query);

    if (!is_null($queryObject['offset'])) $bound[] = $queryObject['offset'];
    if (!is_null($queryObject['limit'])) $bound[] = $queryObject['limit'];

    $bound = implode(', ', $bound);

    return $bound ? " limit $bound" : '';
  }
}
