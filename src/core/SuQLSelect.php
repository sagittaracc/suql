<?php
namespace core;

class SuQLSelect extends SuQLQuery {
  protected $type     = 'select';
  private $select     = [];
  private $from       = null;
  private $where      = [];
  private $having     = [];
  private $join       = [];
  private $group      = [];
  private $order      = [];
  private $modifier   = null;
  private $offset     = null;
  private $limit      = null;
  private $table_list = [];

  public function getSelect() {
    return $this->select;
  }

  public function addField($table, $name, $visible = true) {
    $field = new SuQLFieldName($table, $name);
    $this->select[$field->format('%t.%n')] = new SuQLField($this, $table, $field->format('%t.%n'), $field->format('%a'), $visible, $modifier = []);
  }

  public function hasField($table, $name) {
    $field = new SuQLFieldName($table, $name);
    return isset($this->select[$field->format('%t.%n')]);
  }

  public function getField($table, $name) {
    if ($this->hasField($table, $name)) {
      $field = new SuQLFieldName($table, $name);
      return $this->select[$field->format('%t.%n')];
    } else {
      return null;
    }
  }

  public function addFrom($table) {
    $this->from = $table;
    $this->table_list[] = $table;
  }

  public function getFrom() {
    return $this->from;
  }

  public function addWhere($where) {
    if ($where)
      $this->where[] = $where;
  }

  public function getWhere() {
    return $this->where;
  }

  public function addHaving($having) {
    if ($having)
      $this->having[] = $having;
  }

  public function getHaving() {
    return $this->having;
  }

  public function addJoin($type, $table) {
    $this->join[$table] = new SuQLJoin($this, $table, $type);
    $this->table_list[] = $table;
  }

  public function hasJoin($table) {
    return isset($this->join[$table]);
  }

  public function getJoin($table) {
    return $this->hasJoin($table) ? $this->join[$table] : null;
  }

  public function getJoinList() {
    return $this->join;
  }

  public function addGroup($field) {
    $this->group[] = $field;
  }

  public function getGroup() {
    return $this->group;
  }

  public function addOrder($field, $direction = 'asc') {
    $this->order[] = new SuQLOrder($field, $direction);
  }

  public function getOrder() {
    return $this->order;
  }

  public function addModifier($modifier) {
    $this->modifier = $modifier;
  }

  public function hasModifier() {
    return !is_null($this->modifier);
  }

  public function getModifier() {
    return $this->modifier;
  }

  public function addOffset($offset) {
    if ($offset)
      $this->offset = $offset;
  }

  public function hasOffset() {
    return !is_null($this->offset);
  }

  public function getOffset() {
    return $this->offset;
  }

  public function addLimit($limit) {
    if ($limit)
      $this->limit = $limit;
  }

  public function hasLimit() {
    return !is_null($this->limit);
  }

  public function getLimit() {
    return $this->limit;
  }

  public function getTableList() {
    return $this->table_list;
  }
}
