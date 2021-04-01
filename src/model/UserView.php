<?php

namespace sagittaracc\model;

use \SuQL;

class UserView extends SuQL
{
	public function alias()
	{
		return 'userView';
	}

	public function view()
	{
		return User::find();
	}
}
