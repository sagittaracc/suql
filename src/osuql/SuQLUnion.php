<?php
namespace osuql;

class SuQLUnion {
  private $osuql = null;

  private $type   = 'union';
  private $suql   = '';

  function __construct($osuql, $suql) {
    $this->osuql = $osuql;
    $this->suql = $suql;
  }
}
