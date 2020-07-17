<?php
namespace core;

class SuQLJoin {
  private $oselect = null;

  private $table;
  private $type;
  private $on;

  function __construct($oselect, $table, $type) {
    $this->oselect = $oselect;
    $this->table = $table;
    $this->type = $type;
    $this->on = $this->getOn();
  }

  private function getOn() {
    $scheme        = $this->oselect->getOSuQL()->getRels();
    $tableList     = $this->oselect->getTableList();
    $tableLinks    = array_keys($scheme[$this->table]);
    $possibleLinks = array_intersect($tableLinks, $tableList);
    $targetLink    = array_pop($possibleLinks);
    $on            = $scheme[$this->table][$targetLink];

    return $on;
  }
}
