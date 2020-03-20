<?php
class SuQLHandler
{
	private $stringBuffer;
	private $canonicalQuery;
	private $osuql = [
		'queries' => []
	];
	private $query;

	function __construct() {
		$this->stringBuffer = '';
		$this->canonicalQuery = ['select' => [], 'from' => null, 'where' => null];
		$this->query = 'main';
		$this->osuql['queries'][$this->query] = $this->canonicalQuery;
	}

	public function output() {
		return $this->osuql;
	}

	public function TM_GO_0($ch) {
		$this->query = 'main';
	}

	public function TM_STAY_table_alias($ch) {
		$this->stringBuffer .= $ch;
	}

	public function TM_GO_new_table_alias($ch) {
		$this->query = $this->stringBuffer;
		$this->stringBuffer = '';
		$this->osuql['queries'][$this->query] = $this->canonicalQuery;
	}

	public function TM_GO_select($ch) {
		$this->stringBuffer .= $ch;
	}

	public function TM_STAY_select($ch) {
		$this->stringBuffer .= $ch;
	}

	public function TM_GO_new_select($ch) {
		$this->osuql['queries'][$this->query]['from'] = $this->stringBuffer;
		$this->stringBuffer = '';
	}

	public function TM_GO_field($ch) {
		$this->stringBuffer .= $ch;
	}

	public function TM_STAY_field($ch) {
		$this->stringBuffer .= $ch;
	}

	public function TM_GO_new_field($ch) {
		$this->osuql['queries'][$this->query]['select'][] = $this->stringBuffer;
		$this->stringBuffer = '';
	}

	public function TM_GO_select_end($ch) {
		if ($this->stringBuffer)
			$this->osuql['queries'][$this->query]['select'][] = $this->stringBuffer;
		$this->stringBuffer = '';
	}
}
