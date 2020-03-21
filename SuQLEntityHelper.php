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

	public static function isParentheses($ch)
	{
		return $ch === '(' || $ch === ')';
	}

	public static function isComparisonSymbol($ch)
	{
		return $ch === '>' || $ch === '<' || $ch === '=';
	}

	public static function isPlaceholderSymbol($ch)
	{
		return $ch === '?';
	}

	public static function isWhereClausePossibleSymbol($ch)
	{
		return self::isI($ch)
				|| self::isS($ch)
				|| self::isParentheses($ch)
				|| self::isComparisonSymbol($ch)
				|| self::isPlaceholderSymbol($ch);
	}
}
