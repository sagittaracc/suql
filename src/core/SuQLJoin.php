<?php
namespace core;

class SuQLJoin {
  private $oselect = null;

  private $table;
  private $on;
  private $type;

  function __construct($oselect, $table, $on, $type) {
    $this->oselect = $oselect;
    $this->table = $table;
    $this->on = $on;
    $this->type = $type;
  }
}
