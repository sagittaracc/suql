<?php

namespace app\model;

class FilterUserGroupView extends \PDOSuQL
{
  protected $dbname = 'test';
  public function view()
  {
    return User::find()
               ->field('id', [
                 'filter' => ['equal', ':uid']
               ])
               ->field('login')
             ->join(UserGroup::class)
             ->join(Group::class)
               ->field(['id' => 'gid'])
               ->field(['name' => 'gname']);
  }
}
