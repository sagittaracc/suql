<?php
class SuQLParser
{
	const REGEX_NESTED_QUERY = '/@(?<name>[a-z0-9_]+)\s*=\s*(?<query>.*?);/msi';  // @<var_name> = <query>;

	private $suql;

	function __construct() {
		$this->clear();
	}

	public function clear() {
		$this->suql = null;
	}

	public function setQuery($suql) {
		$this->suql = trim($suql);
	}

	public function getNestedQueries() {
    if (!$this->suql) return null;

		// Looking for the nested queries
    preg_match_all(self::REGEX_NESTED_QUERY, $this->suql, $nestedQueries);
    return array_combine($nestedQueries['name'], $nestedQueries['query']);
	}
}
