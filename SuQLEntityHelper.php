<?php
class SuQLEntityHelper
{
	public static function isI($ch)
	{
		return ord($ch) >= 48 && ord($ch) <= 57
			|| ord($ch) >= 97 && ord($ch) <= 122
			|| $ch === '_';
	}

	public static function isS($ch)
	{
		return in_array(ord($ch), [32, 13, 10]);
	}
}
