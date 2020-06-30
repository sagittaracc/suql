<?php
class OSuQL extends SQLSugarSyntax
{
  private $currentQuery;
  private $currentTable;
  private $currentField;
  private $currentJoinType;

  private $queryList;
  private $tableList;

  function __construct() {
    parent::__construct();
  }

  protected function init() {
    parent::init();
    $this->currentQuery = null;
    $this->currentTable = null;
    $this->currentField = null;
    $this->queryList = [];
    $this->tableList = [];
  }

  public function clear() {
    parent::clear();
    $this->currentQuery = null;
    $this->currentTable = null;
    $this->currentField = null;
    $this->queryList = [];
    $this->tableList = [];
  }

  public function rel($leftTable, $rightTable, $on, $temporary = false) {
    $this->tableList[] = $leftTable;
    $this->tableList[] = $rightTable;

    parent::rel($leftTable, $rightTable, $on, $temporary);

    return $this;
  }

  public function query($name = 'main') {
    parent::addQuery($name);
    $this->currentQuery = $name;
    $this->currentTable = null;
    $this->currentField = null;
    $this->queryList[] = $name;
    return $this;
  }

  public function left() {
    if (!$this->currentTable) return;
    $this->currentJoinType = 'left';
    return $this;
  }

  public function right() {
    if (!$this->currentTable) return;
    $this->currentJoinType = 'right';
    return $this;
  }

  public function field($name, $visible = true) {
    if (!$this->currentTable) return;

    $this->currentField = parent::addField($this->currentQuery, $this->currentTable, $name, $visible);

    return $this;
  }

  public function where($where) {
    parent::addWhere($this->currentQuery, $where);
    return $this;
  }

  public function offset($offset) {
    parent::addOffset($this->currentQuery, $offset);
    return $this;
  }

  public function limit($limit) {
    parent::addLimit($this->currentQuery, $limit);
    return $this;
  }

  public function __call($name, $arguments) {
    // Если есть обработчик $name, то приоритет отдаем ему
    if (method_exists(self::class, $name)) return;
    // Прежде всего должна быть задана query, main по дефолту
    if (!$this->currentQuery) return;
    // Пробуем распознать таблицу или подзапрос
    if ($this->isTable($name) || $this->isQuery($name)) {
      // Запрашиваем из неё или джоиним к текущей таблицы
      if (!$this->currentTable)
        return $this->from($name, $arguments);
      else
        return $this->join($name, $arguments);
    }
    // Здесь только один вариант - запрашивается таблица
    if (!$this->currentTable) return $this->from($name, $arguments);
    // Здесь уже обработка модификаторов (должно быть задано Поле)
    if (!$this->currentField) return;
    // Обрабатываем модификатор
    return $this->modifier($name, $arguments);
  }

  private function isTable($name) {
    return in_array($name, $this->tableList);
  }

  private function isQuery($name) {
    return in_array($name, $this->queryList);
  }

  private function from($table, $arguments) {
    parent::addFrom($this->currentQuery, $table);
    if (!empty($arguments))
      parent::addQueryModifier($this->currentQuery, $arguments[0]);
    $this->currentTable = $table;
    $this->currentJoinType = 'inner';
    return $this;
  }

  private function join($table, $arguments) {
    parent::addJoin($this->currentQuery, $this->currentJoinType, $table);

    $this->currentTable = $table;
    $this->currentJoinType = 'inner';
    return $this;
  }

  private function modifier($name, $arguments) {
    parent::addFieldModifier($this->currentQuery, $this->currentField, $name, $arguments);
    return $this;
  }
}
