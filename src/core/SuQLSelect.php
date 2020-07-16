<?php
namespace core;

class SuQLSelect {
  private $osuql      = null;

  private $type       = 'select';
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

  function __construct($osuql) {
    $this->osuql = $osuql;
  }

  public function getSQLObject() {
    $oselect = [
      'type' => $this->type,
      'select' => [],
    ];

    foreach ($this->select as $field => $options) {
      $oselect['select'][$field] = $options->getSQLObject();
    }

    return $oselect;
  }

  public function addModifier($modifier) {
    $this->modifier = $modifier;
  }

  public function addField($table, $name, $visible = true) {
    $field = new SuQLFieldName($table, $name);
    $fieldId = $field->alias ? $field->alias : $field->format('%t.%n');

    $this->select[$fieldId] = new SuQLField($this, $table, $field->format('%t.%n'), $field->format('%a'), $visible, $modifier = []);

    return $fieldId;
  }

  public function addWhere($where) {
    if ($where)
      $this->where[] = $where;
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
    $scheme        = array_merge($this->osuql->scheme['rel'], $this->osuql->scheme['temp_rel']);
    $tableList     = $this->table_list;
    $tableLinks    = array_keys($scheme[$table]);
    $possibleLinks = array_intersect($tableLinks, $tableList);
    $targetLink    = array_pop($possibleLinks);
    $on            = $scheme[$table][$targetLink];

    $this->join[$table] = new SuQLJoin($this, $table, $on, $type);

    $this->table_list[] = $table;
  }
}
