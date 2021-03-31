<?php

namespace sagittaracc\model;

use \SuQL;

class UserView extends SuQL
{
	public function tableView()
	{
		return ['subquery' => User::find()];
	}
}