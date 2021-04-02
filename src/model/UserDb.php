<?php

namespace sagittaracc\model;

use \SuQL;
use \SuQLExtensionExample;
use \PDOSuQL;

class UserDb extends PDOSuQL
{
	protected $dbname = 'test';

	public function query()
	{
		return 'user';
	}

	public function table()
	{
		return 'users';
	}
}
