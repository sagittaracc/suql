<?php
class TuringMachine
{
	private $handler = null;
	private $currentState;
	public $ch = null;

	function __construct(){}
	
	public function go($state)
	{
		$this->currentState = $state;
		if ($this->handler) {
			$methodName = "TM_GO_$state";
			if (method_exists($this->handler, $methodName))
				$this->handler->$methodName($this->ch);
		}
	}
	
	public function stay($state)
	{
		if ($this->handler) {
			$methodName = "TM_STAY_$state";
			if (method_exists($this->handler, $methodName))
				$this->handler->$methodName($this->ch);
		}
	}
	
	public function setHandler($handler)
	{
		$this->handler = $handler;
	}
	
	public function getCurrentState()
	{
		return $this->currentState;
	}
	
	public function output()
	{
		return $this->handler->output();
	}
}