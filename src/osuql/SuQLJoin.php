<?php
namespace osuql;

class SuQLJoin {
  private $osuql = null;

  private $table;
  private $on;
  private $type;

  function __construct($osuql, $table, $on, $type) {
    $this->osuql = $osuql;
    $this->table = $table;
    $this->on = $on;
    $this->type = $type;
  }
}
