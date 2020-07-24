<?php
use core\SuQLObject;

class OSuQL extends SuQLObject
{
  private $currentQuery = null;
  private $currentTable = null;
  private $currentField = null;
  private $currentJoin = 'inner';
  private $parser;

  function __construct() {
    $this->parser = new OSuQLParser();
  }

  public function clear() {
    parent::clear();
    $this->currentQuery = null;
    $this->currentTable = null;
    $this->currentField = null;
    $this->currentJoin  = 'inner';
    $this->parser->clear();
  }

  public function getSQL($queryList = ['main']) {
    return parent::getSQL($queryList);
  }

  public function run($params = []) {
    return parent::exec($this->currentQuery, $params);
  }

  public function rel($leftTable, $rightTable, $on, $temporary = false) {
    parent::rel($leftTable, $rightTable, $on, $temporary);
    return $this;
  }

  public function query($name = 'main') {
    $this->parser->chain('query');
    $this->currentQuery = $name;
    $this->currentTable = null;
    $this->currentField = null;
    return $this;
  }

  public function select() {
    $this->parser->chain('select')->process($this);
    parent::addSelect($this->currentQuery);
    return $this;
  }

  public function union($table) {
    $this->parser->chain('union');
    parent::addUnionTable($this->currentQuery, 'union', $table);
    return $this;
  }

  public function unionAll($table) {
    $this->parser->chain('union');
    parent::addUnionTable($this->currentQuery, 'unionAll', $table);
    return $this;
  }

  public function left() {
    if (!$this->currentTable) return;
    $this->currentJoin = 'left';
    return $this;
  }

  public function right() {
    if (!$this->currentTable) return;
    $this->currentJoin = 'right';
    return $this;
  }

  public function field($name, $visible = true) {
    if (!$this->currentTable) return;
    $field = parent::getQuery($this->currentQuery)->addField($this->currentTable, $name, $visible);
    $this->currentField = "{$field->name}@{$field->alias}";
    return $this;
  }

  public function where($where) {
    parent::getQuery($this->currentQuery)->addWhere($where);
    return $this;
  }

  public function offset($offset) {
    parent::getQuery($this->currentQuery)->addOffset($offset);
    return $this;
  }

  public function limit($limit) {
    parent::getQuery($this->currentQuery)->addLimit($limit);
    return $this;
  }

  public function __call($name, $arguments) {
    if (!$this->currentQuery) return;

    if (method_exists(parent::getModifierClass(), "mod_$name"))
      return $this->modifier($name, $arguments);

    if (!$this->currentTable)
      return $this->from($name, $arguments);
    else
      return $this->join($name, $arguments);
  }

  private function from($table, $arguments) {
    parent::getQuery($this->currentQuery)->addFrom($table);
    if (!empty($arguments))
      parent::getQuery($this->currentQuery)->addModifier($arguments[0]);
    $this->currentTable = $table;
    return $this;
  }

  private function join($table, $arguments) {
    parent::getQuery($this->currentQuery)->addJoin($this->currentJoin, $table);
    $this->currentTable = $table;
    return $this;
  }

  private function modifier($name, $arguments) {
    parent::getQuery($this->currentQuery)->getField($this->currentTable, $this->currentField)->addModifier($name, $arguments);
    return $this;
  }
}
