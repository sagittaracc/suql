<?php

namespace app\model;

class UserGroupView extends \SuQLView
{
  public function view()
  {
    return User::find()->join(UserGroup::class)->join(Group::class);
  }
}
