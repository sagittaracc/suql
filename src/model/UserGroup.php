<?php

namespace sagittaracc\model;

use \SuQL;

class UserGroup extends SuQL
{
  public function query()
  {
    return 'user_group';
  }

  public function table()
  {
    return 'user_group';
  }

  public function link()
  {
    return [
      User::class => ['user_id' => 'id'],
      Group::class => ['group_id' => 'id'],
    ];
  }
}
