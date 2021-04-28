<?php

namespace suql\modifier\query;

trait SQLDistinctModifier
{
  public function distinct()
  {
    $this->getQuery($this->query())->addModifier('distinct');
    return $this;
  }
}
