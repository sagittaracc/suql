<?php
use core\SuQLObject;

class SuQL extends SuQLObject
{
  private $suql;

  public function clear() {
    parent::clear();
    $this->suql = null;
  }

  public function rel($leftTable, $rightTable, $on, $temporary = false) {
    parent::rel($leftTable, $rightTable, $on, $temporary);
    return $this;
  }

  public function getSQL($queryList = ['main']) {
    return $this->interpret() ? parent::getSQL($queryList) : null;
  }

  public function getSQLObject() {
    return $this->interpret() ? parent::getSQLObject() : null;
  }

  public function query($suql) {
    $this->suql = trim($suql);
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
        parent::addFrom($name, $table);

      else if ($options['type'] === 'join')
        parent::addJoin($name, $options['next'], $table);

      else
        return false;

      if ($options['modifier'] !== '')
        parent::addQueryModifier($name, $options['modifier']);

      if ($options['where'] !== '')
        parent::addWhere($name, $options['where']);

      if ($options['fields'] !== '') {
        $fieldList = SuQLParser::getFieldList($options['fields']);
        for ($i = 0, $n = count($fieldList['name']); $i < $n; $i++) {
          $_name = $fieldList['name'][$i];
          $_alias = $fieldList['alias'][$i];
          $_modifier = $fieldList['modif'][$i];
          $fieldName = parent::addField($name, $table, [$_name => $_alias]);

          $fieldModifierList = SuQLParser::getFieldModifierList($_modifier);
          foreach ($fieldModifierList as $modif => $params) {
            parent::addFieldModifier($name, $fieldName, $modif, $params ? explode(',', $params) : []);
          }
        }
      }
    }

    if (!is_null($clauses['offset'])) parent::addOffset($name, $clauses['offset']);
    if (!is_null($clauses['limit'])) parent::addLimit($name, $clauses['limit']);

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
    parent::addUnionQuery($name, SuQLParser::trimSemicolon($query));
    return true;
  }
}
