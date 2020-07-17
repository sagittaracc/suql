<?php
namespace core;

use builder\SQLAdapter;

class SuQLObject {
  private $queries = [];
  private $scheme  = ['rel' => [], 'temp_rel' => []];
  private $adapter = null;

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

    $this->clear();

    return $SQLBuilder->getSql($queryList);
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
    return $this->rel($leftTable, $rightTable, $on, true);
  }

  public function addSelect($name) {
    $this->queries[$name] = new SuQLSelect($this);
  }

  public function addUnion($name, $query) {
    $this->queries[$name] = new SuQLUnion($this, $query);
  }

  public function getQuery($name) {
    return $this->queries[$name];
  }
}
