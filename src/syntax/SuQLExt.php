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

  public function filterLike($field, $value)
  {
    $this->field($field, [
      'filter' => ['like', $value],
    ]);

    return $this;
  }
}
