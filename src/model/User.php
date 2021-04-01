<?php

namespace sagittaracc\model;

use \SuQL;

class User extends SuQL
{
	public function query()
	{
		return 'user';
	}

	public function table()
	{
		return 'users';
	}
}
