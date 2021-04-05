<?php

namespace app\model;

use \SuQL;

class Group extends SuQL
{
  public function table()
  {
    return 'groups';
  }

  public function relations()
  {
    return [
      UserGroup::class => ['id' => 'group_id'],
    ];
  }
}
