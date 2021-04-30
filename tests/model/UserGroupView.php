<?php

namespace app\model;

use suql\syntax\SuQLView;

class UserGroupView extends SuQLView
{
  public function view()
  {
    return User::find()
    				->select([
    				  'id' => 'uid',
    				  'name' => 'uname'
    				])
    			  ->join(UserGroup::class)
    			  ->join(Group::class)
    			    ->select([
    			      'id' => 'gid',
    			      'name' => 'gname'
    			    ]);
  }
}
