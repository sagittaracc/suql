<?php

namespace app\model;

class ModelNotDefined extends \PDOSuQLTable
{
	// Не задали настройки подключения к базе данных
	
	public function table()
	{
		return 'table';
	}
}