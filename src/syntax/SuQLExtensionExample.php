<?php

// Example: extend SuQL functionality on your own

class SuQLExtensionExample extends SuQL
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
