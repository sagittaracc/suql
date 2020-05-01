<?php
class SuQLParser
{
	// @<var_name> = <query>;
	const REGEX_NESTED_QUERY = '/@(?<name>[a-z0-9_]+)\s*=\s*(?<query>.*?;)/msi';
	/*
	 *	select from <table>
	 *		<field list>
	 *	[where <conditions>]
	 *	[
	 *		(left|right|inner) join <table>
	 *			<field list>
	 *		[where <conditions>]
 	 *	]
	 *	[offset <offset>]
	 *	[limit <limit>]
	 */
	const REGEX_SELECT = '/\s*select\s*from\s*(?<table>[a-z]+)\s*(?<fields>.*?)(where\s*(?<where>.*?))?\s*(?<join>(left|right|inner)\s*join\s*.*?)?\s*(offset\s*(?<offset>\d+))?\s*(limit\s*(?<limit>\d+))?\s*;/msi';

	public static function getNestedQueries($suql) {
    preg_match_all(self::REGEX_NESTED_QUERY, $suql, $nestedQueries);
    return array_combine($nestedQueries['name'], $nestedQueries['query']);
	}

	public static function getMainQuery($suql) {
		return false;
	}

	public static function getSelectClauses($suql) {
		preg_match_all(self::REGEX_SELECT, $suql, $selectClauses);
		return $selectClauses;
	}
}
