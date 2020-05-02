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
	const REGEX_SELECT = '/\s*select\s*from\s*@?(?<table>[a-z0-9_]+)\s*(?<fields>.*?)(where\s*(?<where>.*?))?\s*(?<join>(left|right|inner)\s*join\s*.*?)?\s*(offset\s*(?<offset>\d+))?\s*(limit\s*(?<limit>\d+))?\s*;/msi';
	const REGEX_MAIN_SELECT = '/^;?\s*(?<query>select.*?;)/msi';
	const REGEX_JOIN = '/(?<join_type>left|right|inner)\s*join\s*(?<table>[a-z0-9_]+)/msi';

	public static function getNestedQueries($suql) {
    preg_match_all(self::REGEX_NESTED_QUERY, $suql, $nestedQueries);
    return array_combine($nestedQueries['name'], $nestedQueries['query']);
	}

	public static function getMainQuery($suql) {
		preg_match_all(self::REGEX_MAIN_SELECT, $suql, $main);
		return $main['query'][0];
	}

	public static function getSelectClauses($suql) {
		preg_match_all(self::REGEX_SELECT, $suql, $selectClauses);
		return $selectClauses;
	}

	public static function getJoinedTables($suql) {
		preg_match_all(self::REGEX_JOIN, $suql, $joinedTables);
		return array_combine($joinedTables['join_type'], $joinedTables['table']);
	}
}
