<?php

namespace app\model;

use \SuQL;

class SubUserGroupView extends SuQL
{
  public function view()
  {
    return UserGroupView::find();
  }

  public function normalize()
  {
    $this->setCurrentModel(UserGroupView::class)
         ->field('id')
         ->field('name');

    return $this;
  }
}
