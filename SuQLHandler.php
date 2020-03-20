<?php
class SuQLHandler
{
	private $stringBuffer = '';
	private $osuql = ['select' => [], 'from' => null];
	
	public function output()
	{
		return $this->osuql;
	}

	public function TM_GO_start($ch)
	{
		$this->stringBuffer .= $ch;
	}
	
	public function TM_STAY_start($ch)
	{
		$this->stringBuffer .= $ch;
	}
	
	public function TM_GO_select($ch)
	{
		$this->osuql['from'] = $this->stringBuffer;
		$this->stringBuffer = '';
	}
	
	public function TM_GO_select_all_fields($ch)
	{
		$this->stringBuffer = '*';
	}
	
	public function TM_GO_end($ch)
	{
		$this->osuql['select'][] = $this->stringBuffer;
	}
	
	public function TM_GO_field($ch)
	{
		$this->stringBuffer .= $ch;
	}
	
	public function TM_STAY_field($ch)
	{
		$this->stringBuffer .= $ch;
	}
	
	public function TM_GO_field_finish($ch)
	{
		$this->osuql['select'][] = $this->stringBuffer;
		$this->stringBuffer = '';
	}
}