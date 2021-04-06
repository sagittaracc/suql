<?php

namespace app\model;

use \SuQL;

class SubUserGroupView extends SuQL
{
  public function view()
  {
    return UserGroupView::find()->select(['id', 'name']);
  }
}
