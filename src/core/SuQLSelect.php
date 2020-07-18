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

  public function addModifier($modifier) {
    $this->modifier = $modifier;
  }

  public function hasModifier() {
    return !is_null($this->modifier);
  }

  public function getModifier() {
    return $this->modifier;
  }

  public function getSelect() {
    return $this->select;
  }

  public function getFrom() {
    return $this->from;
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

  public function addWhere($where) {
    if ($where)
      $this->where[] = $where;
  }

  public function addHaving($having) {
    if ($having)
      $this->having[] = $having;
  }

  public function addOrder($field, $direction) {
    $this->order[] = [
      'field' => $field,
      'direction' => $direction,
    ];
  }

  public function addGroup($field) {
    $this->group[] = $field;
  }

  public function addOffset($offset) {
    if ($offset)
      $this->offset = $offset;
  }

  public function addLimit($limit) {
    if ($limit)
      $this->limit = $limit;
  }

  public function addFrom($table) {
    $this->from = $table;
    $this->table_list[] = $table;
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

  public function getTableList() {
    return $this->table_list;
  }
}
