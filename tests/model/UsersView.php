<?php

namespace app\model;

class UsersView extends \PDOSuQLView
{
  protected $dbname = 'test';

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
