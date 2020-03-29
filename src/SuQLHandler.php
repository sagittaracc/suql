<?php
class SuQLHandler
{
	private $stringBuffer1;
	private $stringBuffer2;
	private $stringBuffer3;
	private $stringBuffer4;
	private $arrayBuffer1;
	private $arrayBuffer2;
	private $canonicalQuery;
	private $osuql = [
		'queries' => [],
	];
	private $query; //Текущий подзапрос или запрос
	private $table;	//Текущий select таблицы

	function __construct() {
		$this->stringBuffer1 = '';
		$this->stringBuffer2 = '';
		$this->stringBuffer3 = '';
		$this->stringBuffer4 = '';
		$this->arrayBuffer1 = [];
		$this->arrayBuffer2 = [];
		$this->canonicalQuery = ['select' => [], 'from' => null, 'where' => [], 'join' => [], 'group' => [], 'order' => []];
		$this->query = 'main';
		$this->table = null;
		$this->osuql['queries'][$this->query] = $this->canonicalQuery;
	}

	public function output() {
		return $this->osuql;
	}

	public function TM_GO_0($ch) {
		$this->query = 'main';
		$this->table = null;
	}

	public function TM_STAY_table_alias($ch) {
		$this->stringBuffer1 .= $ch;
	}

	public function TM_GO_new_table_alias($ch) {
		$this->query = $this->stringBuffer1;
		$this->stringBuffer1 = '';
		$this->osuql['queries'][$this->query] = $this->canonicalQuery;
	}

	public function TM_GO_select($ch) {
		$this->stringBuffer1 .= $ch;
	}

	public function TM_STAY_select($ch) {
		$this->stringBuffer1 .= $ch;
	}

	public function TM_GO_new_select($ch) {
		$this->osuql['queries'][$this->query]['from'] = $this->stringBuffer1;
		$this->table = $this->stringBuffer1;
		$this->stringBuffer1 = '';
	}

	public function TM_GO_field($ch) {
		$this->stringBuffer1 .= $ch;
	}

	public function TM_STAY_field($ch) {
		$this->stringBuffer1 .= $ch;
	}

	public function TM_GO_new_field($ch) {
		$this->osuql['queries'][$this->query]['select']["$this->table.$this->stringBuffer1" . ($this->stringBuffer2 ? " as $this->stringBuffer2" : '')] = [
			'field' => "$this->table.$this->stringBuffer1",
			'alias' => $this->stringBuffer2,
		];
		$this->stringBuffer1 = '';
		$this->stringBuffer2 = '';
	}

	public function TM_GO_select_end($ch) {
		if ($this->stringBuffer1)
			$this->osuql['queries'][$this->query]['select']["$this->table.$this->stringBuffer1" . ($this->stringBuffer2 ? " as $this->stringBuffer2" : '')] = [
				'field' => "$this->table.$this->stringBuffer1",
				'alias' => $this->stringBuffer2,
			];
		$this->stringBuffer1 = '';
		$this->stringBuffer2 = '';
	}

	public function TM_GO_field_alias($ch) {
		$this->stringBuffer2 .= $ch;
	}

	public function TM_STAY_field_alias($ch) {
		$this->stringBuffer2 .= $ch;
	}

	public function TM_STAY_where_clause($ch) {
		$this->stringBuffer1 .= $ch;
	}

	public function TM_GO_where_clause_end($ch) {
		$this->osuql['queries'][$this->query]['where'][] = trim($this->stringBuffer1);
		$this->stringBuffer1 = '';
	}

	public function TM_STAY_join_clause($ch) {
		$this->stringBuffer1 .= $ch;
	}

	public function TM_GO_join_clause_end($ch) {

	}

	public function TM_GO_joined_select($ch) {
		$this->stringBuffer2 .= $ch;
	}

	public function TM_STAY_joined_select($ch) {
		$this->stringBuffer2 .= $ch;
	}

	public function TM_GO_new_joined_select($ch) {
		$this->osuql['queries'][$this->query]['join'][] = [
			'table' => $this->stringBuffer2,
			'on' => $this->stringBuffer1,
		];
		$this->table = $this->stringBuffer2;
		$this->stringBuffer1 = '';
		$this->stringBuffer2 = '';
	}

	public function TM_GO_field_modifier($ch) {
		if ($this->stringBuffer3)
			$this->arrayBuffer1[$this->stringBuffer3] = $this->arrayBuffer2;

		$this->stringBuffer3 = '';
		$this->arrayBuffer2 = [];
	}

	public function TM_STAY_field_modifier($ch) {
		$this->stringBuffer3 .= $ch;
	}

	public function TM_GO_field_modifier_param_expects($ch) {
		if ($this->stringBuffer4)
			$this->arrayBuffer2[] = $this->stringBuffer4;

		$this->stringBuffer4 = '';
	}

	public function TM_GO_field_modifier_param($ch) {
		$this->stringBuffer4 .= $ch;
	}

	public function TM_STAY_field_modifier_param($ch) {
		$this->stringBuffer4 .= $ch;
	}

	public function TM_GO_apply_field_modifiers($ch) {
		foreach ($this->arrayBuffer1 as $modifier => $params) {
			$modifier_handler = "mod_$modifier";
			if (method_exists($this, $modifier_handler))
				$this->$modifier_handler($this->table, $this->stringBuffer1, $this->stringBuffer2, $params);
		}

		$this->stringBuffer1 = '';
		$this->stringBuffer2 = '';
		$this->arrayBuffer1 = [];
	}

	private function mod_group($table, $field, $alias, $params) {
		$this->osuql['queries'][$this->query]['group'][] = "$table.$field";
	}

	private function mod_count($table, $field, $alias, $params) {
		$this->osuql['queries'][$this->query]['select']["count($table.$field)" . ($alias ? " as $alias" : '')] = [
			'field' => "count($table.$field)",
			'alias' => $alias,
		];
	}

	private function mod_desc($table, $field, $alias, $params) {
		$this->osuql['queries'][$this->query]['select']["$table.$field" . ($alias ? " as $alias" : '')] = [
			'field' => "$table.$field",
			'alias' => $alias,
		];
		$this->osuql['queries'][$this->query]['order'][] = [
			'field' => "$table.$field",
			'direction' => 'desc',
		];
	}

	private function mod_asc($table, $field, $alias, $params) {
		$this->osuql['queries'][$this->query]['select']["$table.$field" . ($alias ? " as $alias" : '')] = [
			'field' => "$table.$field",
			'alias' => $alias,
		];
		$this->osuql['queries'][$this->query]['order'][] = [
			'field' => "$table.$field",
			'direction' => 'asc',
		];
	}

	private function mod_somefunc($table, $field, $alias, $params) {
		if (!isset($this->osuql['queries'][$this->query]['select']["$table.$field" . ($alias ? " as $alias" : '')])) {
			$this->osuql['queries'][$this->query]['select']["$table.$field" . ($alias ? " as $alias" : '')] = [
				'field' => "$table.$field",
				'alias' => $alias,
				'modifier' => []
			];
		}

		$this->osuql['queries'][$this->query]['select']["$table.$field" . ($alias ? " as $alias" : '')]['modifier']['somefunc'] = $params;

	}

	private function mod_anotherfunc($table, $field, $alias, $params) {
		if (!isset($this->osuql['queries'][$this->query]['select']["$table.$field" . ($alias ? " as $alias" : '')])) {
			$this->osuql['queries'][$this->query]['select']["$table.$field" . ($alias ? " as $alias" : '')] = [
				'field' => "$table.$field",
				'alias' => $alias,
				'modifier' => []
			];
		}


		$this->osuql['queries'][$this->query]['select']["$table.$field" . ($alias ? " as $alias" : '')]['modifier']['anotherfunc'] = $params;
	}
}
