<?php

namespace sagittaracc\model;

use \SuQL;

class User extends SuQL
{
	public function tableName()
	{
		return 'users';
	}
}