<?php
use core\SuQLObject;

class SuQL extends SuQLObject
{
  private $suql = null;

  public function clear() {
    parent::clear();
    $this->suql = null;
  }

  public function rel($leftTable, $rightTable, $on, $temporary = false) {
    parent::rel($leftTable, $rightTable, $on, $temporary);
    return $this;
  }

  public function getSQL($queryList = ['main']) {
    return parent::getSQL($queryList);
  }

  public function run($params = []) {
    return parent::exec('main', $params);
  }

  public function query($suql) {
    $this->suql = trim($suql);
    $this->interpret();
    return $this;
  }

  private function interpret() {
    if (!$this->suql) return false;

    $queryList = SuQLParser::getQueryList($this->suql);
    foreach ($queryList as $name => $query) {
      $handler = SuQLParser::getQueryHandler($query);

      if (!$handler || !$this->$handler($name, $query))
        return false;
    }

    return true;
  }

  private function SELECT($name, $query)
  {
    parent::addSelect($name);

    $clauses = SuQLParser::parseSelect($query);

    foreach ($clauses['tables'] as $table => $options) {
      if ($options['type'] === 'from')
        parent::getQuery($name)->addFrom($table);

      else if ($options['type'] === 'join')
        parent::getQuery($name)->addJoin($options['next'], $table);

      else
        return false;

      if ($options['modifier'] !== '')
        parent::getQuery($name)->addModifier($options['modifier']);

      if ($options['where'] !== '')
        parent::getQuery($name)->addWhere($options['where']);

      if ($options['fields'] !== '') {
        $fieldList = SuQLParser::getFieldList($options['fields']);
        for ($i = 0, $n = count($fieldList['name']); $i < $n; $i++) {
          $_name = $fieldList['name'][$i];
          $_alias = $fieldList['alias'][$i];
          $_modifier = $fieldList['modif'][$i];
          $field = parent::getQuery($name)->addField($table, [$_name => $_alias]);

          $fieldModifierList = SuQLParser::getFieldModifierList($_modifier);
          foreach ($fieldModifierList as $modif => $params) {
            parent::getQuery($name)->getField($table, [$field->name => $field->alias])->addModifier($modif, $params ? explode(',', $params) : []);
          }
        }
      }
    }

    if (!is_null($clauses['offset'])) parent::getQuery($name)->addOffset($clauses['offset']);
    if (!is_null($clauses['limit'])) parent::getQuery($name)->addLimit($clauses['limit']);

    return true;
  }

  private function INSERT($name, $query) {
    return true;
  }

  private function UPDATE($name, $query) {
    return true;
  }

  private function DELETE($name, $query) {
    return true;
  }

  private function UNION($name, $query) {
    parent::addUnion($name, SuQLParser::trimSemicolon($query));
    return true;
  }

  private function COMMAND($name, $query) {
    return true;
  }
}
