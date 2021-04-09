<?php

namespace app\model;

use \SuQL;

class User extends \SuQL
{
  protected function modifierList()
  {
    return array_merge(
      parent::modifierList(),
      [
        'SQLUserNameModifier',
      ]
    );
  }

  public function table()
  {
    return 'users';
  }

  public function relations()
  {
    return [
      UserGroup::class => ['id' => 'user_id'],
      UserGroupView::class => ['id' => 'user_id'],
    ];
  }

  public function new()
  {
    $this->field('*');
    $this->field('register', [
      'where' => ['DATE($) = CURDATE()'],
    ], false);

    return $this;
  }
}
