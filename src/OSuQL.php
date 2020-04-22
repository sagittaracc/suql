<?php
class OSuQL
{
  private $osuql;

  private $currentQuery;
  private $currentTable;
  private $currentField;

  private $queryList;
  private $tableList;

  private function init() {
    $this->osuql = [];
    $this->currentQuery = null;
    $this->currentTable = null;
    $this->currentField = null;
    $this->queryList = [];
    $this->tableList = [];
  }

  function __construct() {
    $this->init();
  }

  public function getSQLObject() {
    $osuql = $this->osuql;
    $this->flush();
    return $osuql;
  }

  public function rel($leftTable, $rightTable, $on, $linkType) {
    $this->tableList[] = $leftTable;
    $this->tableList[] = $rightTable;
    return $this;
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

    $this->osuql['queries'][$this->currentQuery]['select'][$alias ? $alias : "{$this->currentTable}.$name"] = [
      'table' => $this->currentTable,
      'field' => $name,
      'alias' => $alias,
    ];
    return $this;
  }

  public function where($where) {
    return $this;
  }

  public function offset($offset) {
    return $this;
  }

  public function limit($limit) {
    return $this;
  }

  public function flush() {
    $this->init();
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
    return $this;
  }

  private function join($table) {
    $this->currentTable = $table;
    return $this;
  }
}

/*

$db = (new OSuQL)->rel('users', 'user_group', 'id = user_id', OSuQL::OneToMany)
                 ->rel('user_group', 'groups', 'group_id = id', OSuQL::ManyToOne);

$db->query('All')
    ->users()
    ->left()->user_group()
    ->left()->groups()
      ->field('name', 'g_name')
      ->field('name', 'count')->group()->count()
    ->where('g_name = "admin"');

$db->query()
    ->All()
    ->where('id > 3')
    ->flush();

Start over after flushing
$db->query()->users();

*/
