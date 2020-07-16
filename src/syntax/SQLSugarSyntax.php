<?php
use core\SuQLSpecialSymbols;
use core\SuQLTableName;
use core\SuQLFieldName;

class SQLSugarSyntax
{
  private $osuql;
  private $scheme;
  private $adapter;

  function __construct() {
    $this->init();
  }

  protected function init() {
    $this->osuql = [];
    $this->scheme = ['rel' => [], 'temp_rel' => []];
    $this->adapter = null;
  }

  public function clear() {
    $this->osuql = [];
    $this->scheme['temp_rel'] = [];
  }

  public function drop() {
    $this->osuql = [];
    $this->scheme['temp_rel'] = [];
    $this->scheme['rel'] = [];
    $this->adapter = null;
  }

  public function setAdapter($adapter) {
    if (SQLAdapter::exists($adapter))
      $this->adapter = $adapter;

    return $this;
  }

  public function getSQLObject() {
    $osuql = !empty($this->osuql) ? $this->osuql : null;
    $this->clear();
    return $osuql;
  }

  public function getSQL($queryList) {
    if (!$this->adapter) return null;

    if ($queryList === 'all')
      $queryList = $this->getAllTheQueryList();

    if (!is_array($queryList)) return null;

    $classBuilder = SQLAdapter::get($this->adapter);
    $SQLBuilder = new $classBuilder($this);
    $SQLBuilder->run($queryList);
    $this->clear();
    return $SQLBuilder->getSql($queryList);
  }

  public function rel($leftTable, $rightTable, $on, $temporary = false) {
    $leftTable = new SuQLTableName($leftTable);
    $rightTable = new SuQLTableName($rightTable);

    if ($leftTable->alias)
      $on = str_replace($leftTable->alias, $leftTable->name, $on);

    if ($rightTable->alias)
      $on = str_replace($rightTable->alias, $rightTable->name, $on);

    $this->scheme[$temporary ? 'temp_rel' : 'rel'][$leftTable->name][$rightTable->name] = $on;
    $this->scheme[$temporary ? 'temp_rel' : 'rel'][$rightTable->name][$leftTable->name] = $on;
  }

  public function temp_rel($leftTable, $rightTable, $on) {
    return $this->rel($leftTable, $rightTable, $on, true);
  }

  public function addSelect($name) {
    $this->osuql['queries'][$name] = [
      'type'       => 'select',
      'select'     => [],
      'from'       => null,
      'where'      => [],
      'having'     => [],
      'join'       => [],
      'group'      => [],
      'order'      => [],
      'modifier'   => null,
      'offset'     => null,
      'limit'      => null,
      'table_list' => [],
    ];
  }

  public function addUnionQuery($name, $query) {
    $this->osuql['queries'][$name] = [
      'type' => 'union',
      'suql' => $query,
    ];
  }

  public function addUnion($query, $table) {
    $var_prefix = SuQLSpecialSymbols::$prefix_declare_variable;
    $this->osuql['queries'][$query]['type'] = 'union';
    if (empty($this->osuql['queries'][$query]['suql'])) {
      $this->osuql['queries'][$query]['suql'] = $var_prefix . $table;
    } else {
      $this->osuql['queries'][$query]['suql'] .= ' union ' . $var_prefix . $table;
    }
  }

  public function addUnionAll($query, $table) {
    $var_prefix = SuQLSpecialSymbols::$prefix_declare_variable;
    $this->osuql['queries'][$query]['type'] = 'union';
    if (empty($this->osuql['queries'][$query]['suql'])) {
      $this->osuql['queries'][$query]['suql'] = $var_prefix . $table;
    } else {
      $this->osuql['queries'][$query]['suql'] .= ' union all ' . $var_prefix . $table;
    }
  }

  public function addQueryModifier($query, $modifier) {
    $this->osuql['queries'][$query]['modifier'] = $modifier;
  }

  public function addField($query, $table, $name, $visible = true) {
    $field = new SuQLFieldName($table, $name);
    $fieldId = $field->alias ? $field->alias : $field->format('%t.%n');

    $this->osuql['queries'][$query]['select'][$fieldId] = [
      'table' => $table,
      'field' => $field->format('%t.%n'),
      'alias' => $field->format('%a'),
      'visible' => $visible,
      'modifier' => [],
    ];

    return $fieldId;
  }

  public function addWhere($query, $where) {
    if ($where)
      $this->osuql['queries'][$query]['where'][] = $where;
  }

  public function addOffset($query, $offset) {
    if ($offset)
      $this->osuql['queries'][$query]['offset'] = $offset;
  }

  public function addLimit($query, $limit) {
    if ($limit)
      $this->osuql['queries'][$query]['limit'] = $limit;
  }

  public function addFrom($query, $table) {
    $this->osuql['queries'][$query]['from'] = $table;
    $this->osuql['queries'][$query]['table_list'][] = $table;
  }

  public function addJoin($query, $type, $table) {
    $scheme        = array_merge($this->scheme['rel'], $this->scheme['temp_rel']);
    $tableList     = $this->osuql['queries'][$query]['table_list'];
    $tableLinks    = array_keys($scheme[$table]);
    $possibleLinks = array_intersect($tableLinks, $tableList);
    $targetLink    = array_pop($possibleLinks);
    $on            = $scheme[$table][$targetLink];

    $this->osuql['queries'][$query]['join'][$table] = [
      'table' => $table,
      'on'    => $on,
      'type'  => $type,
    ];

    $this->osuql['queries'][$query]['table_list'][] = $table;
  }

  public function addFieldModifier($query, $field, $name, $arguments) {
    $this->osuql['queries'][$query]['select'][$field]['modifier'][$name] = $arguments;
  }

  public function get() {
    return $this->osuql;
  }

  public function getAllTheQueryList() {
    return array_keys($this->osuql['queries']);
  }

  public function &getQuery($query) {
    if (isset($this->osuql['queries'][$query]))
      $queryObject = &$this->osuql['queries'][$query];
    else
      $queryObject = null;

    return $queryObject;
  }

  public function getQueryType($query) {
    return $this->osuql['queries'][$query]['type'];
  }

  public function getQuerySuqlString($query) {
    return $this->osuql['queries'][$query]['suql'];
  }

  public function getFieldModifiers($queryObject) {
    $list = [];

    foreach ($queryObject['select'] as $field => $options)
      if (!empty($options['modifier']))
        $list[$field] = $options['modifier'];

    return $list;
  }
}
