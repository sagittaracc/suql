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

	public function link()
	{
		return [
			UserGroup::class => ['id' => 'user_id'],
		];
	}
}