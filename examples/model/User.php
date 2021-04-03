<?php

namespace app\model;

use \SuQL;
use \MySuQLExt;

class User extends MySuQLExt
{
  public function query()
  {
    return 'user';
  }

  public function table()
  {
    return 'users';
  }

  public function link()
  {
    return [
      UserGroup::class => ['id' => 'user_id'],
    ];
  }
}
