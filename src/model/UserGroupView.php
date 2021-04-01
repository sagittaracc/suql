<?php

namespace sagittaracc\model;

use \SuQL;

class UserGroupView extends SuQL
{
  public function query()
  {
    return 'user_group_view';
  }

  public function view()
  {
    return User::find()->join(UserGroup::class)->join(Group::class);
  }
}
