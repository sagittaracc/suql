<?php

namespace app\model;

use \SuQL;

class UsersView extends SuQL
{
  public function view()
  {
    return User::find()
               ->field(['id' => 'uid'])
               ->field(['login' => 'username'])
             ->join(UserGroup::class)
             ->join(Group::class)
               ->field(['id' => 'gid'])
               ->field(['name' => 'groupname']);
  }
}
