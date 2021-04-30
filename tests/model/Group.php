<?php

namespace app\model;

use suql\syntax\SuQLTable;

class Group extends SuQLTable
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
