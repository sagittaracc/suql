<?php

namespace app\model;

class FilterUserGroupView extends \PDOSuQL
{
  protected $dbname = 'test';
  public function view()
  {
    return User::find()
               ->field('id', [
                 'filter' => ['equal', ':id']
               ])
             ->join(UserGroup::class)
             ->join(Group::class);
  }
}
