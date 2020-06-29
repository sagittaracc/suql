<?php
class SQLSugarSyntax
{
  private $osuql;
  private $scheme;
  private $adapter;

  function __construct() {
    $this->clear();
    $this->scheme = [];
  }

  public function clear() {
    $this->osuql = [];
    $this->scheme['temp_rel'] = [];
  }

  public function drop() {
    $this->clear();
    $this->scheme = [];
  }

  public function setAdapter($adapter) {
    if (SQLAdapter::exists($adapter))
      $this->adapter = $adapter;

    return $this;
  }

  public function getSQLObject() {
    $osuql = $this->osuql;
    $this->clear();
    return $osuql;
  }

  public function getSQL() {
    if (!$this->adapter) return null;

    $classBuilder = SQLAdapter::get($this->adapter);
    $SQLBuilder = new $classBuilder($this->getSQLObject());
    $SQLBuilder->run();
    return $SQLBuilder->getSql();
  }

  public function rel($leftTable, $rightTable, $on, $temporary = false) {
    $leftTable = new Helper\SuQLTableDef($leftTable);
    $rightTable = new Helper\SuQLTableDef($rightTable);

    if (!is_null($leftTable->alias))
      $on = str_replace($leftTable->alias, $leftTable->name, $on);

    if (!is_null($rightTable->alias))
      $on = str_replace($rightTable->alias, $rightTable->name, $on);

    $this->scheme[$temporary ? 'temp_rel' : 'rel'][$leftTable->name][$rightTable->name] = $on;
    $this->scheme[$temporary ? 'temp_rel' : 'rel'][$rightTable->name][$leftTable->name] = $on;
  }

  public function temp_rel($leftTable, $rightTable, $on) {
    return $this->rel($leftTable, $rightTable, $on, true);
  }

  public function addQuery($name) {
    $this->osuql['queries'][$name] = [
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

  public function addQueryModifier($query, $modifier) {
    $this->osuql['queries'][$query]['modifier'] = $modifier;
  }

  public function addField($query, $table, $name, $visible = true) {
    $field = is_string($name) ? [$name => ''] : $name;
    if (!is_array($field)) return;
    foreach ($field as $name => $alias) break;

    $fieldName = $alias ? $alias : "$table.$name";
    $this->osuql['queries'][$query]['select'][$fieldName] = [
      'table' => $table,
      'field' => "$table.$name",
      'alias' => $alias,
      'visible' => $visible,
      'modifier' => [],
    ];

    return $fieldName;
  }

  public function addWhere($query, $where) {
    if (!$where) return;
    $this->osuql['queries'][$query]['where'][] = $where;
  }

  public function addOffset($query, $offset) {
    if (is_null($offset)) return;
    $this->osuql['queries'][$query]['offset'] = $offset;
  }

  public function addLimit($query, $limit) {
    if (is_null($limit)) return;
    $this->osuql['queries'][$query]['limit'] = $limit;
  }

  public function addFrom($query, $table) {
    $this->osuql['queries'][$query]['from'] = $table;
    $this->osuql['queries'][$query]['table_list'][] = $table;
  }

  public function addJoin($query, $type, $table) {
    $rel = isset($this->scheme['rel'][$table])
            ? 'rel'
            : (isset($this->scheme['temp_rel'])
                ? 'temp_rel'
                : null);

    if (!$rel) return;

    $possibleTableLinks = array_keys($this->scheme[$rel][$table]);
    $tableToJoinTo = array_intersect($possibleTableLinks, $this->osuql['queries'][$query]['table_list']);
    $on = count($tableToJoinTo) === 1 ? $this->scheme[$rel][$tableToJoinTo[0]][$table] : null;

    if (!$on) return;

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
}
