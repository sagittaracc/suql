<?php
class OSuQL
{
  private $osuql;

  private $currentQuery;
  private $currentTable;
  private $currentField;

  private $errors;

  private function init() {
    $this->currentQuery = null;
    $this->currentTable = null;
    $this->currentField = null;
    $this->errors = [];
  }

  function __construct() {
    $this->init();
  }

  public function getSQLObject() {
    return $this->osuql;
  }

  public function rel($leftTable, $rightTable, $on, $linkType) {
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
    return $this;
  }

  public function left() {
    return $this;
  }

  public function right() {
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
    if (method_exists(self::class, $name)) return;
    if (!$this->currentQuery) return;
    if (!$this->currentTable) return $this->from($name);
  }

  private function from($table) {
    $this->osuql['queries'][$this->currentQuery]['from'] = $table;
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

$db->query('main')
    ->All()
    ->where('id > 3')
    ->flush();

Start over after flushing
$db->query()->users();

*/
