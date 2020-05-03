<?php
class SuQLParser
{
	// @<var_name> = <query>;
	const REGEX_NESTED_QUERY = '/@(?<name>\w+)\s*=\s*(?<query>.*?;)/msi';
	const REGEX_MAIN_SELECT = '/^;?\s*(?<query>select.*?;)/msi';
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
 	const REGEX_SELECT = "/(?<type>from|join)\s+@?(?<table>\w+)\s+((?<fields>.*?)(where\s+(?<where>.*?)\s*)?)?(?<next>left|right|inner|limit\s+(?<offset>\d+)\s*,\s*(?<limit>\d+)|;)/msi";
	// <field_name[.modif1[(<params>)].modif2.modif3...][@field_alias], ...
	const REGEX_FIELDS = '/(?<name>[\*\w]+)(?<modif>.*?)(@(?<alias>\w+))?\s*,?\s*$/msi';
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

	public static function parseSelect($suql) {
		preg_match_all(self::REGEX_SELECT, $suql, $clauses);
		array_unshift($clauses['next'], array_pop($clauses['next']));
		$tables = ['tables' => [], 'offset' => null, 'limit' => null];
		for ($i = 0, $n = count($clauses['table']); $i < $n; $i++) {
			$tables['tables'][$clauses['table'][$i]] = [
				'type' => strtolower($clauses['type'][$i]),
				'fields' => $clauses['fields'][$i],
				'where' => $clauses['where'][$i],
				'next' => strtolower($clauses['next'][$i]),
			];
		}
		if ($clauses['offset'][count($clauses['offset']) - 1] !== '')
			$tables['offset'] = $clauses['offset'][count($clauses['offset']) - 1];
			if ($clauses['limit'][count($clauses['limit']) - 1] !== '')
				$tables['limit'] = $clauses['limit'][count($clauses['limit']) - 1];
		return $tables;
	}

	public static function getFieldList($suql) {
		preg_match_all(self::REGEX_FIELDS, $suql, $fieldList);
		return $fieldList;
	}

	public static function getFieldModifierList($suql) {
		preg_match_all(self::REGEX_FIELD_MODIFIERS, $suql, $fieldModifierList);
		return array_combine($fieldModifierList['name'], $fieldModifierList['params']);
	}
}
