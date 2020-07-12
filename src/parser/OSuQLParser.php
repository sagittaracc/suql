<?php
class OSuQLParser
{
  private $chain;

  function __construct() {
    $this->chain = ['prev' => 'none', 'curr' => 'none'];
  }

  public function clear() {
    $this->chain = ['prev' => 'none', 'curr' => 'none'];
  }

  public function chain($name) {
    if ($this->chain['curr'])
      $this->chain['prev'] = $this->chain['curr'];

    $this->chain['curr'] = $name;
    return $this;
  }

  public function process($context) {
    $chain = 'chain_' . $this->chain['prev'] . '_' . $this->chain['curr'];
    if (method_exists($this, $chain))
      $this->$chain($context);
  }

  private function chain_none_select($context) {
    $context->query('main');
  }

  private function chain_union_select($context) {
    $context->query('main');
  }
}
