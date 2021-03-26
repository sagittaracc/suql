<?php
namespace sagittaracc\helpers;

class RegexpHelper {
  private $regex;
  private $flags;
  protected $sequenceList = [];

  function __construct($regex, $flags = '') {
    if (count(explode('/', $regex)) > 1) {
      $parts = explode('/', $regex);
      $regex = $parts[1];
      $flags = $parts[2];
    }

    $this->setRegex($regex);
    $this->setFlags($flags);
  }

  public function setRegex($regex) {
    $this->regex = $regex;
    return $this;
  }

  public function setFlags($flags) {
    $this->flags = $flags;
    return $this;
  }

  public function registerSequence($name, $set) {
    $sequence = new RegexpSequenceHelper($name, $set);

    if ($sequence->valid())
      $this->sequenceList[$sequence->name] = $sequence->set;

    return $this;
  }

  private function buildRegex() {
    return str_replace(
      array_keys($this->sequenceList),
      array_values($this->sequenceList),
      "/{$this->regex}/{$this->flags}"
    );
  }

  public function match($subject) {
    $regex = $this->buildRegex();
    preg_match($regex, $subject, $matches);
    return empty($matches) ? false : (isset($matches[1]) ? $matches[1] : true);
  }

  public function match_all($subject) {
    $regex = $this->buildRegex();
    preg_match_all($regex, $subject, $matches);
    return $matches;
  }
}
