<?php
class SuQLParser
{
	const REGEX_NESTED_QUERY = '/@(?<name>[a-z0-9_]+)\s*=\s*(?<query>.*?);/msi';  // @<var_name> = <query>;

	public static function getNestedQueries($suql) {
    preg_match_all(self::REGEX_NESTED_QUERY, $suql, $nestedQueries);
    return array_combine($nestedQueries['name'], $nestedQueries['query']);
	}

	public static function getMainQuery($suql) {
		return false;
	}
}
