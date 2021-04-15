<?php

namespace app\model;

class FilterUsersView extends \PDOSuQL
{
  protected $dbname = 'test';
  public function view()
  {
    return UsersView::find();
  }

  public function normalize()
  {
    $this->setCurrentModel(UsersView::class)
         ->filter('uid', ['equal', ':uid'])
         // ->filter('username', ['like', ':username'])
         ->filter('gid', ['equal', ':gid']);
         // ->filter('groupname', ['like', ':groupname']);

    return $this;
  }
}
