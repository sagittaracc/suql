<?php
namespace core;

class SuQLCommand extends SuQLQuery {
  private $instruction;
  private $params;

  function __construct($osuql, $instruction, $params) {
    parent::__construct($osuql);
    $this->instruction = $instruction;
    $this->params = $params;
  }

  public function getType() {
    return 'command';
  }

  public function getSemantic() {
    return 'cmd';
  }
}
