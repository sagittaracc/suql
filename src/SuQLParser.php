<?php
class SuQLParser
{
	// @<var_name> = <query>;
	const REGEX_NESTED_QUERY = '/@(?<name>\w+)\s*=\s*(?<query>.*?;)/msi';
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
	const REGEX_SELECT = '/\s*select\s*from\s*@?(?<table>\w+)\s*(?<fields>.*?)(where\s*(?<where>.*?))?\s*(?<join>(left|right|inner)\s*join\s*.*?)?\s*(offset\s*(?<offset>\d+))?\s*(limit\s*(?<limit>\d+))?\s*;/msi';
	const REGEX_MAIN_SELECT = '/^;?\s*(?<query>select.*?;)/msi';
	const REGEX_JOIN = '/(?<join_type>left|right|inner)\s*join\s*(?<table>\w+)/msi';
	// <field_name[.modif1[(<params>)].modif2.modif3...][@field_alias], ...
	const REGEX_FIELDS = '/(?<name>\w+)(?<modif>.*?)(@(?<alias>\w+))?\s*,?\s*$/msi';
	const REGEX_FIELD_MODIFIERS = '/.(?<name>\w+)(\((?<params>.*?)\))?/msi';

	public static function getQueryHandler($suql) {
		return 'SELECT';
	}

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

	public static function getFieldList($suql) {
		preg_match_all(self::REGEX_FIELDS, $suql, $fieldList);
		return $fieldList;
	}

	public static function getFieldModifierList($suql) {
		preg_match_all(self::REGEX_FIELD_MODIFIERS, $suql, $fieldModifierList);
		return array_combine($fieldModifierList['name'], $fieldModifierList['params']);
	}

	public static function getJoinedTables($suql) {
		preg_match_all(self::REGEX_JOIN, $suql, $joinedTables);
		return array_combine($joinedTables['join_type'], $joinedTables['table']);
	}
}
