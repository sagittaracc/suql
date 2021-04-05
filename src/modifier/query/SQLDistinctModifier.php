<?php

trait SQLDistinctModifier
{
  public function distinct()
  {
    $this->getQuery($this->query())->addModifier('distinct');
    return $this;
  }
}
