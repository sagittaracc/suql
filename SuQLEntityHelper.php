<?php
class SuQLEntityHelper
{
	public static function isI($ch)
	{
		return ord($ch) >= 48 && ord($ch) <= 57
			|| ord($ch) >= 97 && ord($ch) <= 122
			|| $ch === '_';
	}
}