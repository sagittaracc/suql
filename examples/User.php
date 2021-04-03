<?php

namespace app\model;

use \SuQL;
use \SuQLExtensionExample;

class User extends SuQLExtensionExample
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
