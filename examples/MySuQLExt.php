<?php

// Example: extend SuQL functionality on your own
// Don't forget to extend your model from this class

abstract class MySuQLExt extends SuQL
{
  protected function modifierList()
  {
    return array_merge(
      parent::modifierList(),
      [
        'SQLArithmeticModifier',
      ]
    );
  }

  public function max($field)
  {
    $this->field($field, [
      'max',
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
