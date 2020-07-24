<?php
namespace core;

class SuQLCommand extends SuQLQuery {
  private $instruction;
  private $args;

  function __construct($osuql, $instruction, $args) {
    parent::__construct($osuql);
    $this->instruction = $instruction;
    $this->args = $args;
  }

  public function getType() {
    return 'command';
  }

  public function getSemantic() {
    return 'cmd';
  }

  public function getInstruction() {
    return $this->instruction;
  }

  public function getArgs() {
    return $this->args;
  }
}
