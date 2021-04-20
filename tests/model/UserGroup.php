<?php

namespace app\model;

class UserGroup extends \SuQLTable
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
