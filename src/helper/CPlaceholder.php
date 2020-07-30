<?php
namespace Helper;

class CPlaceholder {
  private $str;
  private $quote;

  function __construct($str) {
    $this->setString($str);
    $this->setQuote("'");
  }

  public function setString($str) {
    if (is_string($str))
      $this->str = $str;

    return $this;
  }

  public function setQuote($quote) {
    if (is_string($quote))
      $this->quote = $quote;

    return $this;
  }

  public function bind() {
    $params = func_get_args();

    foreach ($params as $param) {
      $this->str = preg_replace('/\?/', $this->format($param), $this->str, 1);
    }

    return $this->str;
  }

  private function format($param) {
    switch (gettype($param)) {
      case 'boolean':
      case 'integer':
      case 'double':
        return $param;

      case 'string':
        return "{$this->quote}$param{$this->quote}";

      case 'array':
        return CArray::isSequential($param)
                 ? '['.implode(',', array_map(array($this, 'format'), $param)).']'
                 : '#array';

      case 'object':
        return '#object';

      case 'resource':
        return '#resource';

      case 'NULL':
        return 'NULL';

      default:
        return '';
    }
  }
}
