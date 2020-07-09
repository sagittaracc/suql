<?php
class OSuQLParser
{
  private $chain;

  function __construct() {
    $this->chain = ['prev' => null, 'curr' => null];
  }

  public function clear() {
    $this->chain = ['prev' => null, 'curr' => null];
  }

  public function chain($name) {
    if ($this->chain['curr'])
      $this->chain['prev'] = $this->chain['curr'];

    $this->chain['curr'] = $name;
    return $this;
  }

  public function process($context) {
    $chain = $this->chain['prev'] . '_' . $this->chain['curr'];
    if (method_exists($this, $chain))
      $this->$chain($context);
  }

  private function _select($context) {
    $context->query('main');
  }

  private function union_select($context) {
    $context->query('main');
  }
}
