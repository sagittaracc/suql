<?php

// Example: max function

class SuQLExt extends SuQL
{
  public function max($field)
  {
    $this->field($field, [
      'max' => [],
    ]);

    return $this;
  }
}
