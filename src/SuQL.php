<?php
class SuQL
{
	private $suql = null;

	private $builder = null;
	private $availableBuilders = [
		'mysql' => 'MySQLBuilder'
	];

	private $tm = null;
	private $SQLBuilder = null;

	private $error = null;

	function __construct($suql)
	{
		$this->suql = trim($suql);
	}

	public function setBuilder($builder)
	{
		if (isset($this->availableBuilders[$builder]))
			$this->builder = $builder;

		return $this;
	}

	public function getError()
	{
		return $this->error;
	}

	public function pureSQL()
	{
		if ($this->interpret())
			return $this->buildSQL();

		return false;
	}

	public function getSQLObjectBeforePreparing()
	{
		if ($this->interpret())
			return $this->tm->output();
		else
			return null;
	}

	public function getSQLObjectAfterPreparing()
	{
		if ($this->interpret()) {
			$this->buildSQL();
			return $this->SQLBuilder->getSQLObject();
		}

		return null;
	}

	public static function toSql($suql, $builder)
	{
		return (new self($suql))->setBuilder($builder);
	}

	public static function toSqlObject($suql, $builder, $phase)
	{
		if ($phase === 'beforePreparing')
			return (new self($suql))->setBuilder($builder)->getSQLObjectBeforePreparing();
		else if ($phase === 'afterPreparing')
			return (new self($suql))->setBuilder($builder)->getSQLObjectAfterPreparing();
		else
			return null;
	}

	public function __toString()
	{
		return $this->pureSQL();
	}

	private function interpret()
	{
		$this->tm = new TuringMachine();
		$this->tm->setHandler(new SuQLHandler());
		$this->tm->go('0');

		try {
			for ($i = 0; $i < strlen($this->suql); $i++) {
				$this->tm->ch = substr($this->suql, $i, 1);

				switch ($this->tm->getCurrentState()) {
					case '0':
						if (SuQLEntityHelper::isS($this->tm->ch)) ;
						else if ($this->tm->ch ==='#') $this->tm->go('table_alias');
						else if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->go('select');
						else {throw new Exception($i);}
						break;
					case 'table_alias':
						if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->stay('table_alias');
						else if (SuQLEntityHelper::isS($this->tm->ch)) ;
						else if ($this->tm->ch === '=') $this->tm->go('new_table_alias');
						else {throw new Exception($i);}
						break;
					case 'select':
						if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->stay('select');
						else if ($this->tm->ch === '.') $this->tm->go('select_modifier_expects');
						else if (SuQLEntityHelper::isS($this->tm->ch)) $this->tm->go('new_select_expects');
						else if ($this->tm->ch === '{') $this->tm->go('new_select');
						else {throw new Exception($i);}
						break;
					case 'select_modifier_expects':
						if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->go('select_modifier');
						else {throw new Exception($i);}
						break;
					case 'select_modifier':
						if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->stay('select_modifier');
						else if (SuQLEntityHelper::isS($this->tm->ch)) $this->tm->go('new_select_expects');
						else if ($this->tm->ch === '{') $this->tm->go('new_select');
						else {throw new Exception($i);}
						break;
					case 'new_table_alias':
						if (SuQLEntityHelper::isS($this->tm->ch)) ;
						else if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->go('select');
						else {throw new Exception($i);}
						break;
					case 'new_select_expects':
						if (SuQLEntityHelper::isS($this->tm->ch)) ;
						else if ($this->tm->ch === '{') $this->tm->go('new_select');
						else {throw new Exception($i);}
						break;
					case 'new_select':
						if (SuQLEntityHelper::isS($this->tm->ch)) ;
						else if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->go('field');
						else if ($this->tm->ch === '*') $this->tm->go('field');
						else if ($this->tm->ch === '}') $this->tm->go('select_end');
						else {throw new Exception($i);}
						break;
					case 'field':
						if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->stay('field');
						else if (SuQLEntityHelper::isS($this->tm->ch)) $this->tm->go('new_field_expects');
						else if ($this->tm->ch === ',') $this->tm->go('new_field');
						else if ($this->tm->ch === '}') $this->tm->go('select_end');
						else if ($this->tm->ch === '@') $this->tm->go('field_alias_expects');
						else if ($this->tm->ch === '.') $this->tm->go('field_modifier');
						else {throw new Exception($i);}
						break;
					case 'select_end':
						if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->go('joined_select');
						else if (SuQLEntityHelper::isS($this->tm->ch)) ;
						else if ($this->tm->ch === ';') $this->tm->go('0');
						else if ($this->tm->ch === '~') $this->tm->go('where_clause_expects');
						else if ($this->tm->ch === '[') $this->tm->go('offset_limit_clause');
						else {throw new Exception($i);}
						break;
					case 'new_field_expects':
						if ($this->tm->ch === ',') $this->tm->go('new_field');
						else if ($this->tm->ch === '}') $this->tm->go('select_end');
						else if (SuQLEntityHelper::isS($this->tm->ch)) ;
						else {throw new Exception($i);}
						break;
					case 'new_field':
						if (SuQLEntityHelper::isS($this->tm->ch)) ;
						else if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->go('field');
						else {throw new Exception($i);}
						break;
					case 'field_alias_expects':
						if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->go('field_alias');
						else {throw new Exception($i);}
						break;
					case 'field_alias':
						if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->stay('field_alias');
						else if (SuQLEntityHelper::isS($this->tm->ch)) $this->tm->go('new_aliased_field_expects');
						else if ($this->tm->ch === ',') $this->tm->go('new_field');
						else if ($this->tm->ch === '}') $this->tm->go('select_end');
						else if ($this->tm->ch === '.') $this->tm->go('field_modifier');
						else {throw new Exception($i);}
						break;
					case 'field_modifier':
						if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->stay('field_modifier');
						else if ($this->tm->ch === ',') {
							$this->tm->go('field_modifier');
							$this->tm->go('apply_field_modifiers');
						}
						else if ($this->tm->ch === '}') {
							$this->tm->go('field_modifier');
							$this->tm->go('apply_field_modifiers');
							$this->tm->go('select_end');
						}
						else if ($this->tm->ch === '(') $this->tm->go('field_modifier_param_expects');
						else if (SuQLEntityHelper::isS($this->tm->ch)) $this->tm->go('new_field_modifier_expects');
						else if ($this->tm->ch === '.') $this->tm->go('field_modifier');
						else {throw new Exception($i);}
						break;
					case 'field_modifier_param_expects':
						if (SuQLEntityHelper::isS($this->tm->ch)) ;
						else if (SuQLEntityHelper::isParamPossibleSymbol($this->tm->ch)) $this->tm->go('field_modifier_param');
						else {throw new Exception($i);}
						break;
					case 'field_modifier_param':
						if (SuQLEntityHelper::isParamPossibleSymbol($this->tm->ch)) $this->tm->stay('field_modifier_param');
						else if ($this->tm->ch === ',') $this->tm->go('field_modifier_param_expects');
						else if ($this->tm->ch === ')') {
							$this->tm->go('field_modifier_param_expects');
							$this->tm->swith('field_modifier');
						}
						else {throw new Exception($i);}
						break;
					case 'new_field_modifier_expects':
						if (SuQLEntityHelper::isS($this->tm->ch)) ;
						else if ($this->tm->ch === ',') {
							$this->tm->go('field_modifier');
							$this->tm->go('apply_field_modifiers');
						}
						else if ($this->tm->ch === '}') {
							$this->tm->go('field_modifier');
							$this->tm->go('apply_field_modifiers');
							$this->tm->go('select_end');
						}
						else {throw new Exception($i);}
						break;
					case 'apply_field_modifiers':
						if (SuQLEntityHelper::isS($this->tm->ch)) ;
						else if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->go('field');
						else if ($this->tm->ch === ';') $this->tm->go('0');
						else if ($this->tm->ch === '~') $this->tm->go('where_clause_expects');
						else if ($this->tm->ch === '[') $this->tm->go('offset_limit_clause');
						else {throw new Exception($i);}
						break;
					case 'new_aliased_field_expects':
						if (SuQLEntityHelper::isS($this->tm->ch)) ;
						else if ($this->tm->ch === ',') $this->tm->go('new_field');
						else if ($this->tm->ch === '}') $this->tm->go('select_end');
						else {throw new Exception($i);}
						break;
					case 'where_clause_expects':
						if (SuQLEntityHelper::isS($this->tm->ch)) ;
						else if ($this->tm->ch === '{') $this->tm->go('where_clause');
						else {throw new Exception($i);}
						break;
					case 'where_clause':
						if (SuQLEntityHelper::isWhereClausePossibleSymbol($this->tm->ch)) $this->tm->stay('where_clause');
						else if ($this->tm->ch === '}') $this->tm->go('where_clause_end');
						else {throw new Exception($i);}
						break;
					case 'where_clause_end':
						if (SuQLEntityHelper::isS($this->tm->ch)) ;
						else if ($this->tm->ch === '[') $this->tm->go('offset_limit_clause');
						else if ($this->tm->ch === ';') $this->tm->go('0');
						else if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->go('joined_select');
						else {throw new Exception($i);}
						break;
					case 'offset_limit_clause':
						if (SuQLEntityHelper::isS($this->tm->ch)) ;
						else if (SuQLEntityHelper::isN($this->tm->ch)) $this->tm->go('offset_or_limit');
						else {throw new Exception($i);}
						break;
					case 'offset_or_limit':
						if (SuQLEntityHelper::isN($this->tm->ch)) $this->tm->stay('offset_or_limit');
						else if (SuQLEntityHelper::isS($this->tm->ch)) $this->tm->go('offset_or_limit_undefined');
						else if ($this->tm->ch === ',') $this->tm->go('limit_expects');
						else if ($this->tm->ch === ']') $this->tm->go('offset_limit_clause_end');
						else {throw new Exception($i);}
						break;
					case 'offset_or_limit_undefined':
						if (SuQLEntityHelper::isS($this->tm->ch)) $this->tm->stay('offset_or_limit_undefined');
						else if ($this->tm->ch === ',') $this->tm->go('limit_expects');
						else if ($this->tm->ch === ']') $this->tm->go('offset_limit_clause_end');
						else {throw new Exception($i);}
						break;
					case 'limit_expects':
						if (SuQLEntityHelper::isS($this->tm->ch)) ;
						else if (SuQLEntityHelper::isN($this->tm->ch)) $this->tm->go('limit');
						else {throw new Exception($i);}
						break;
					case 'limit':
						if (SuQLEntityHelper::isN($this->tm->ch)) $this->tm->stay('limit');
						else if (SuQLEntityHelper::isS($this->tm->ch)) $this->tm->go('offset_limit_clause_end_expects');
						else if ($this->tm->ch === ']') $this->tm->go('offset_limit_clause_end');
						else {throw new Exception($i);}
						break;
					case 'offset_limit_clause_end_expects':
						if (SuQLEntityHelper::isS($this->tm->ch)) $this->tm->stay('offset_limit_clause_end_expects');
						else if ($this->tm->ch === ']') $this->tm->go('offset_limit_clause_end');
						else {throw new Exception($i);}
						break;
					case 'offset_limit_clause_end':
						if (SuQLEntityHelper::isS($this->tm->ch)) ;
						else if ($this->tm->ch === ';') $this->tm->go('0');
						else {throw new Exception($i);}
						break;
					case 'joined_select':
						if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->stay('joined_select');
						else if (SuQLEntityHelper::isS($this->tm->ch)) $this->tm->go('new_joined_select_expects');
						else if ($this->tm->ch === '{') $this->tm->go('new_joined_select');
						else {throw new Exception($i);}
						break;
					case 'new_joined_select_expects':
						if (SuQLEntityHelper::isS($this->tm->ch)) ;
						else if ($this->tm->ch === '{') $this->tm->go('new_joined_select');
						else {throw new Exception($i);}
						break;
					case 'new_joined_select':
						if (SuQLEntityHelper::isS($this->tm->ch)) ;
						else if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->go('field');
						else if ($this->tm->ch === '*') $this->tm->go('field');
						else if ($this->tm->ch === '}') $this->tm->go('select_end');
						else {throw new Exception($i);}
						break;
				}
			}
		} catch (Exception $e) {
			$this->error = SuQLLog::error($this->suql, $e->getMessage());
			return false;
		}

		return true;
	}

	private function buildSQL()
	{
		if (!$this->builder) return null;

		$classBuilder = $this->availableBuilders[$this->builder];
		if (!class_exists($classBuilder)) return null;

		$this->SQLBuilder = new $classBuilder($this->tm->output());
		$this->SQLBuilder->run();
		return $this->SQLBuilder->getSql();
	}
}
