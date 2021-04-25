<?php
namespace suql\core;

class SuQLJoin {
  private $oselect = null;

  private $table;
  private $type;
  private $on;

  function __construct($oselect, $table, $type) {
    $this->oselect = $oselect;
    $this->table = $table;
    $this->type = $type;
    $this->on = $this->getLink();
  }

  private function getLink() {
    $scheme        = $this->oselect->getOSuQL()->getScheme()->getRels();
    $tableList     = $this->oselect->getTableList();
    $tableLinks    = array_keys($scheme[$this->table]);
    $possibleLinks = array_intersect($tableLinks, $tableList);
    $targetLink    = array_pop($possibleLinks);
    $on            = $scheme[$this->table][$targetLink];

    return $on;
  }

  public function getTable() {
    return $this->table;
  }

  public function getType() {
    return $this->type;
  }

  public function getOn() {
    return $this->on;
  }
}
