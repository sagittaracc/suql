<?php

namespace app\model;

class FilterUsersView extends \PDOSuQL
{
  protected $dbname = 'test';
  public function view()
  {
    return UsersView::find()
             ->filter('uid', ['equal', ':uid'])
             ->filter('username', ['like', ':username'])
             ->filter('gid', ['equal', ':gid'])
             ->filter('group_name', ['like', ':group_name']);
  }
}
