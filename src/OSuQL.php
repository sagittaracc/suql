<?php
class OSuQL
{
  private $osuql;

  private $scheme;

  private $currentQuery;
  private $currentTable;
  private $tablesInQuery;
  private $currentField;

  private $queryList;
  private $tableList;

  private $adapter;

  function __construct() {
    $this->clear();
    $this->scheme = [];
  }

  public function setAdapter($adapter) {
    if (SQLAdapter::exists($adapter))
      $this->adapter = $adapter;

    return $this;
  }

  private function clear() {
    $this->osuql = [];
    $this->currentQuery = null;
    $this->currentTable = null;
    $this->tablesInQuery = [];
    $this->currentField = null;
    $this->queryList = [];
    $this->tableList = [];
    $this->scheme['temp_rel'] = [];
  }

  public function getSQLObject() {
    $osuql = $this->osuql;
    $this->clear();
    return $osuql;
  }

  public function rel($leftTable, $rightTable, $on, $temporary = false) {
    $this->tableList[] = $leftTable;
    $this->tableList[] = $rightTable;

    $on = explode('=', $on);
    $on[0] = $leftTable . '.' . trim($on[0]);
    $on[1] = $rightTable . '.' . trim($on[1]);
    $on = implode(' = ', $on);

    $this->scheme[$temporary ? 'temp_rel' : 'rel'][$leftTable][$rightTable] = $on;
    $this->scheme[$temporary ? 'temp_rel' : 'rel'][$rightTable][$leftTable] = $on;

    return $this;
  }

  public function temp_rel($leftTable, $rightTable, $on) {
    return $this->rel($leftTable, $rightTable, $on, true);
  }

  public function query($name = 'main') {
    $this->osuql['queries'][$name] = [
      'select'   => [],
      'from'     => null,
      'where'    => [],
      'having'   => [],
      'join'     => [],
      'group'    => [],
      'order'    => [],
      'modifier' => null,
      'offset'   => null,
      'limit'    => null,
    ];
    $this->currentQuery = $name;
    $this->currentTable = null;
    $this->currentField = null;
    $this->queryList[] = $name;
    return $this;
  }

  public function left() {
    if (!$this->currentTable) return;
    return $this;
  }

  public function right() {
    if (!$this->currentTable) return;
    return $this;
  }

  public function field($name, $alias = '') {
    if (!$this->currentTable) return;

    $fieldName = $alias ? $alias : "{$this->currentTable}.$name";
    $this->osuql['queries'][$this->currentQuery]['select'][$fieldName] = [
      'table' => $this->currentTable,
      'field' => $name,
      'alias' => $alias,
      'modifier' => [],
    ];
    $this->currentField = $fieldName;

    return $this;
  }

  public function where($where) {
    $this->osuql['queries'][$this->currentQuery]['where'][] = $where;
    return $this;
  }

  public function offset($offset) {
    $this->osuql['queries'][$this->currentQuery]['offset'] = $offset;
    return $this;
  }

  public function limit($limit) {
    $this->osuql['queries'][$this->currentQuery]['limit'] = $limit;
    return $this;
  }

  public function drop() {
    $this->clear();
    $this->scheme = [];
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
        return $this->from($name);
      else
        return $this->join($name);
    }
    // Здесь только один вариант - запрашивается таблица
    if (!$this->currentTable) return $this->from($name);
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

  private function from($table) {
    $this->osuql['queries'][$this->currentQuery]['from'] = $table;
    $this->currentTable = $table;
    $this->tablesInQuery[$this->currentQuery][] = $table;
    return $this;
  }

  private function join($table) {
    $on = $this->getJoinLinkFor($table);
    if (!$on) return;

    $this->osuql['queries'][$this->currentQuery]['join'][$table] = [
      'table' => $table,
      'on'    => $on,
    ];

    $this->currentTable = $table;
    $this->tablesInQuery[$this->currentQuery][] = $table;
    return $this;
  }

  private function modifier($name, $arguments) {
    $this->osuql['queries'][$this->currentQuery]['select'][$this->currentField]['modifier'][$name] = $arguments;
    return $this;
  }

  public function getJoinLinkFor($table) {
    $rel = isset($this->scheme['rel'][$table])
            ? 'rel'
            : (isset($this->scheme['temp_rel'])
                ? 'temp_rel'
                : null);

    if (!$rel) return null;

    $possibleTableLinks = array_keys($this->scheme[$rel][$table]);
    $tableToJoinTo = array_intersect($possibleTableLinks, $this->tablesInQuery[$this->currentQuery]);
    return count($tableToJoinTo) === 1 ? $this->scheme[$rel][$tableToJoinTo[0]][$table] : null;
  }
}
