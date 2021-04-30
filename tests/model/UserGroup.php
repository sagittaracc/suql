<?php

namespace app\model;

use suql\syntax\SuQLTable;

class UserGroup extends SuQLTable
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
