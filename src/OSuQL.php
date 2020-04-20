<?php
class OSuQL
{
  function __construct() {

  }

  public function rel($leftTable, $rightTable, $on, $linkType) {
    return $this;
  }

  public function query($name) {
    return $this;
  }

  public function left() {}
  public function right() {}

  public function field($name, $alias) {

  }

  public function where($where) {

  }

  public function offset($offset) {

  }

  public function limit($limit) {

  }

  public function __call($name, $arguments) {
    // Everything else are modifiers
    if (method_exists(self, $name)) return;
    // if the $name is the $query name
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

*/
