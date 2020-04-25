<?php
class OSuQL
{
  private $osuql;

  private $scheme;

  private $currentQuery;
  private $currentTable;
  private $tablesInQuery;
  private $currentField;

  private $currentJoinType;

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

  public function getSQL() {
    if (!$this->adapter) return null;

    $classBuilder = SQLAdapter::get($this->adapter);
    $SQLBuilder = new $classBuilder($this->getSQLObject());
		$SQLBuilder->run();
		return $SQLBuilder->getSql();
  }

  public function rel($leftTable, $rightTable, $on, $temporary = false) {
    if (is_array($leftTable)) {
      $on = str_replace(array_values($leftTable), array_keys($leftTable), $on);
      $leftTable = array_keys($leftTable)[0];
    }

    if (is_array($rightTable)) {
      $on = str_replace(array_values($rightTable), array_keys($rightTable), $on);
      $rightTable = array_keys($rightTable)[0];
    }

    $this->tableList[] = $leftTable;
    $this->tableList[] = $rightTable;

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
    $this->tablesInQuery[$name] = [];
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

  public function field($name, ...$options) {
    if (!$this->currentTable) return;

    if (count($options) === 0) {
      $visible = true;
      $alias = '';
    }

    if (count($options) === 1) {
      if (is_bool($options[0])) {
        $visible = $options[0];
        $alias = '';
      } else {
        $visible = true;
        $alias = $options[0];
      }
    }

    if (count($options) === 2) {
      $alias = $options[0];
      $visible = $options[1];
    }

    $fieldName = $alias ? $alias : "{$this->currentTable}.$name";
    $this->osuql['queries'][$this->currentQuery]['select'][$fieldName] = [
      'table' => $this->currentTable,
      'field' => "{$this->currentTable}.$name",
      'alias' => $alias,
      'visible' => $visible,
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
    $this->currentJoinType = 'inner';
    return $this;
  }

  private function join($table) {
    $on = $this->getJoinLinkFor($table);
    if (!$on) return;

    $this->osuql['queries'][$this->currentQuery]['join'][$table] = [
      'table' => $table,
      'on'    => $on,
      'type'  => $this->currentJoinType,
    ];

    $this->currentTable = $table;
    $this->tablesInQuery[$this->currentQuery][] = $table;
    $this->currentJoinType = 'inner';
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
