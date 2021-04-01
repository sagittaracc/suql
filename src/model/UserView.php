<?php

namespace sagittaracc\model;

use \SuQL;

class UserView extends SuQL
{
	public function query()
	{
		return 'userView';
	}

	public function view()
	{
		return User::find()->select(['id', 'name']);
	}
}
