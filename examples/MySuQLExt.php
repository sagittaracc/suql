<?php

// Example: extend SuQL functionality on your own
// Don't forget to extend your model from this class

class MySuQLExt extends SuQL
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

  /**
   *
   * Now we can use it something like this:
   *   Model::find()->max(<field>)->getRawSql()
   * 
   * See the example testSuQLExtension in the tests/SuQLTest.php file
   */
  public function max($field)
  {
    $this->field($field, [
      'max' => [],
    ]);

    return $this;
  }

  /**
   *
   * Now we can use it something like this:
   *   Model::find()->filterLike('name', 'yuriy')->getRawSql()
   *
   * See the example testSuQLExtension in the tests/SuQLTest.php file
   */
  public function filterLike($field, $value)
  {
    $this->field($field, [
      'filter' => ['like', $value],
    ]);

    return $this;
  }
}
