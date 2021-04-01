<?php

namespace sagittaracc\model;

use \SuQL;

class User extends SuQL
{
	public function alias()
	{
		return 'u';
	}

	public function table()
	{
		return 'users';
	}
}
