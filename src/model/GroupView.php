<?php

namespace sagittaracc\model;

use \SuQL;

class GroupView extends SuQL
{
  public function query()
  {
    return 'groupView';
  }

  public function view()
  {
    return UserView::find();
  }
}
