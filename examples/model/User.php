<?php

namespace app\model;

use \SuQL;
use \MySuQLExt;

class User extends MySuQLExt
{
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
}
