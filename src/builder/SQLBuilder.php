<?php
class SQLBuilder
{
  private $SQLObject = null;
  private $sql = null;
  protected $sqlTemplate = "#select##from##join##where##group##having##order##limit#";

  function __construct($SQLObject)
  {
    $this->SQLObject = $SQLObject;
  }

  public function getSQLObject()
  {
    return $this->SQLObject;
  }

  public function getSql()
  {
    return $this->sql;
  }

  public function run()
  {
    if (!$this->SQLObject)
      return;

    $this->sql = trim($this->buildQuery('main'));
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
    $sqlTemplate = $this->sqlTemplate;

    $this->setQuery($query, $this->prepareQuery($query));

    $sqlTemplate = str_replace('#select#', $this->buildSelect($query), $sqlTemplate);
    $sqlTemplate = str_replace('#from#'  , $this->buildFrom($query),   $sqlTemplate);
    $sqlTemplate = str_replace('#join#'  , $this->buildJoin($query),   $sqlTemplate);
    $sqlTemplate = str_replace('#group#' , $this->buildGroup($query),  $sqlTemplate);
    $sqlTemplate = str_replace('#where#' , $this->buildWhere($query),  $sqlTemplate);
    $sqlTemplate = str_replace('#having#', $this->buildHaving($query), $sqlTemplate);
    $sqlTemplate = str_replace('#order#' , $this->buildOrder($query),  $sqlTemplate);
    $sqlTemplate = str_replace('#limit#' , $this->buildLimit($query),  $sqlTemplate);

    return $sqlTemplate;
  }

  private function prepareQuery($query) {
    $modifierClass = class_exists('SQLModifier')
      ? 'SQLModifier'
      : 'SQLBaseModifier';

    $queryObject = $this->getQuery($query);

    foreach ($queryObject['select'] as $field => $options) {
      if (empty($options['modifier']))
        continue;

      foreach ($options['modifier'] as $modifier => $params) {
        $modifier_handler = "mod_$modifier";
  			if (method_exists($modifierClass, $modifier_handler))
  				$modifierClass::$modifier_handler($queryObject, $field);
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
      $nestedQuery = $this->buildQuery($from);
      return " from ($nestedQuery) {$from}";
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
        ? "(".$this->buildQuery($table).") $table"
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

    // TODO: Сделать обработку имен вложенных запросов ПЕРЕД сборкой where секции
    // SuQLParser НЕ должен использоваться в SQLBuilder
    $nestedQueryNames = SuQLParser::getNestedQueryNames($where);
    foreach ($nestedQueryNames as $name) {
      if ($this->getQuery($name)) {
        $nestedQuery = $this->buildQuery($name);
        $where = str_replace("#$name", "($nestedQuery)", $where);
      }
    }

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
