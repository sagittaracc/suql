<?php

namespace app\model;

use \SuQL;

class UserGroupView extends SuQL
{
  public function view()
  {
    return User::find()->join(UserGroup::class)->join(Group::class);
  }
}
