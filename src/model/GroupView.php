<?php

namespace sagittaracc\model;

use \SuQL;

class GroupView extends SuQL
{
  public function alias()
  {
    return 'groupView';
  }

  public function view()
  {
    return UserView::find();
  }
}
