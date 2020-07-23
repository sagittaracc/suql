<?php
namespace core;

use builder\SQLAdapter;

class SuQLObject {
  private $queries = [];
  private $scheme  = ['rel' => [], 'temp_rel' => []];
  private $adapter = null;
  private $db      = null;

  public function clear() {
    $this->queries = [];
    $this->scheme['temp_rel'] = [];
  }

  public function drop() {
    $this->queries = [];
    $this->scheme['rel'] = [];
    $this->scheme['temp_rel'] = [];
    $this->adapter = null;
  }

  public function setAdapter($adapter) {
    if (SQLAdapter::exists($adapter))
      $this->adapter = $adapter;

    return $this;
  }

  public function setDb($db) {
    if ($db instanceof IDb)
      $this->db = $db;

    return $this;
  }

  public function getAdapter() {
    return $this->adapter;
  }

  public function getSQL($queryList) {
    if (!$this->adapter) return null;

    if ($queryList === 'all')
      $queryList = $this->getFullQueryList();

    if (!is_array($queryList)) return null;

    $classBuilder = SQLAdapter::get($this->adapter);
    $SQLBuilder = new $classBuilder($this);
    $SQLBuilder->run($queryList);
    $sqlList = $SQLBuilder->getSql($queryList);

    $this->clear();

    return $sqlList;
  }

  public function exec($name, $params = []) {
    if (!$this->db) return null;

    if (!$this->hasQuery($name)) return false;

    return $this->db->setQuery($this->getSQL([$name]))
                    ->bindParams($params)
                    ->fetchAll();
  }

  public function getFullQueryList() {
    return array_keys($this->queries);
  }

  public function rel($leftTable, $rightTable, $on, $temporary = false) {
    $leftTable = new SuQLTableName($leftTable);
    $rightTable = new SuQLTableName($rightTable);

    if ($leftTable->alias)
      $on = str_replace($leftTable->format("%a."), $leftTable->format("%n."), $on);

    if ($rightTable->alias)
      $on = str_replace($rightTable->format("%a."), $rightTable->format("%n."), $on);

    $this->scheme[$temporary ? 'temp_rel' : 'rel'][$leftTable->name][$rightTable->name] = $on;
    $this->scheme[$temporary ? 'temp_rel' : 'rel'][$rightTable->name][$leftTable->name] = $on;
  }

  public function temp_rel($leftTable, $rightTable, $on) {
    return $this->rel($leftTable, $rightTable, $on, $temporary = true);
  }

  public function getRels() {
    return array_merge($this->scheme['rel'], $this->scheme['temp_rel']);
  }

  public function hasRelBetween($table1, $table2) {
    return isset($this->scheme['rel'][$table1][$table2])
        || isset($this->scheme['temp_rel'][$table1][$table2]);
  }

  public function getRelTypeBetween($table1, $table2) {
    if (isset($this->scheme['rel'][$table1][$table2]))
      return 'rel';
    else if (isset($this->scheme['temp_rel'][$table1][$table2]))
      return 'temp_rel';
    else
      return null;
  }

  public function getRelBetween($table1, $table2) {
    if ($this->hasRelBetween($table1, $table2))
      return $this->scheme[$this->getRelTypeBetween($table1, $table2)][$table1][$table2];
    else
      return null;
  }

  public function addSelect($name) {
    $this->queries[$name] = new SuQLSelect($this);
  }

  public function addUnion($name, $query) {
    $this->queries[$name] = new SuQLUnion($this, $query);
  }

  public function addUnionTable($name, $unionType, $table) {
    if (!isset($this->queries[$name]))
      $this->queries[$name] = new SuQLUnion($this, $table);
    else
      $this->queries[$name]->addUnionTable($unionType, $table);
  }

  public function getQuery($name) {
    return $this->queries[$name];
  }

  public function hasQuery($name) {
    return isset($this->queries[$name]);
  }
}
