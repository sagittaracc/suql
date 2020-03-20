<?php
require('TuringMachine.php');
require('SuQLHandler.php');
require('SuQLEntityHelper.php');
require('SuQLLog.php');

class SuQL
{
	private $suql = null;
	private $params = [];

	private $tm = null;

	function __construct($suql, $params)
	{
		$this->suql = trim($suql);
		$this->params = $params;
	}

	public function execute()
	{
		return $this->interpret()
								->buildSQL();
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
						else throw new Exception($i);
						break;
					case 'table_alias':
						if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->stay('table_alias');
						else if (SuQLEntityHelper::isS($this->tm->ch)) ;
						else if ($this->tm->ch === '=') $this->tm->go('new_table_alias');
						else throw new Exception($i);
						break;
					case 'select':
						if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->stay('select');
						else if (SuQLEntityHelper::isS($this->tm->ch)) ;
						else if ($this->tm->ch === '{') $this->tm->go('new_select');
						else throw new Exception($i);
						break;
					case 'new_table_alias':
						if (SuQLEntityHelper::isS($this->tm->ch)) ;
						else if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->go('select');
						else throw new Exception($i);
						break;
					case 'new_select':
						if (SuQLEntityHelper::isS($this->tm->ch)) ;
						else if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->go('field');
						else if ($this->tm->ch === '*') $this->tm->go('field');
						else if ($this->tm->ch === '}') $this->tm->go('select_end');
						else throw new Exception($i);
						break;
					case 'field':
						if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->stay('field');
						else if (SuQLEntityHelper::isS($this->tm->ch)) ;
						else if ($this->tm->ch === ',') $this->tm->go('new_field');
						else if ($this->tm->ch === '}') $this->tm->go('select_end');
						else throw new Exception($i);
						break;
					case 'select_end':
						if ($this->tm->ch === ';') $this->tm->go('0');
						else throw new Exception($i);
						break;
					case 'new_field':
						if (SuQLEntityHelper::isS($this->tm->ch)) ;
						else if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->go('field');
						else throw new Exception($i);
						break;
				}
			}
		} catch (Exception $e) {
			SuQLLog::error($this->suql, $e->getMessage());
		}

		return $this;
	}

	private function buildSQL()
	{
		return $this->tm->output();
	}
}
