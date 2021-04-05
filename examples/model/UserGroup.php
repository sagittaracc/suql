<?php

namespace app\model;

use \SuQL;

class UserGroup extends SuQL
{
  public function table()
  {
    return 'user_group';
  }

  public function relations()
  {
    return [
      User::class => ['user_id' => 'id'],
      Group::class => ['group_id' => 'id'],
    ];
  }
}
