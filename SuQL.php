<?php
require('TuringMachine.php');
require('SuQLHandler.php');
require('SuQLEntityHelper.php');

class SuQL
{
	private $suql = null;
	private $params = [];
	
	private $tm = null;

	function __construct($suql, $params)
	{
		$this->suql = $suql;
		$this->params = $params;
	}
	
	public function execute()
	{
		return $this->interpret();
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
						if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->go('start');
						else throw new Exception($i);
						break;
					case 'start':
						if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->stay('start');
						else if ($this->tm->ch === '{') $this->tm->go('select');
						else throw new Exception($i);
						break;
					case 'select':
						if ($this->tm->ch === '*') $this->tm->go('select_all_fields');
						else if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->go('field');
						else throw new Exception($i);
						break;
					case 'field':
						if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->stay('field');
						else if ($this->tm->ch === '}') $this->tm->go('end');
						else if ($this->tm->ch === ',') $this->tm->go('field_finish');
						else throw new Exception($i);
						break;
					case 'field_finish':
						if (SuQLEntityHelper::isI($this->tm->ch)) $this->tm->go('field');
						else throw new Exception($i);
						break;
					case 'select_all_fields':
						if ($this->tm->ch === '}') $this->tm->go('end');
						else if ($this->tm->ch === ',') $this->tm->go('field_finish');
						else throw new Exception($i);
						break;
					case 'end':
						if ($i <> strlen($this->suql)) throw new Exception($i);
						break;
				}
			}
			
			if ($this->tm->getCurrentState() <> 'end') throw new Exception($i);
		} catch (Exception $e) {
			die('Не удалось обработать запрос в позиции ' . $e->getMessage());
		}
		
		return $this->tm->output();
	}
}