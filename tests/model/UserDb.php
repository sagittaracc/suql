<?php

namespace app\model;

class UserDb extends \PDOSuQLTable
{
  protected $dbname = 'ug';

  public function table()
  {
    return 'users';
  }

  public function relations()
  {
    return [
      UserGroup::class => ['id' => 'user_id']
    ];
  }

  public function getGroupName()
  {
    $this->join(UserGroup::class)
           ->field(['user_id' => 'id'])
         ->join(Group::class)
           ->field(['name' => 'group_name']);

    return $this;
  }
}
