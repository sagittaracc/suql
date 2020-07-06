<?php
namespace Helper;

class CRegExp {
	private $regex;
	private $flags;
	private $sequenceList = [];

	function __construct($regex, $flags = '') {
		if (count(explode('/', $regex)) > 1) {
			$parts = explode('/', $regex);
			$regex = $parts[1];
			$flags = $parts[2];
		}

		$this->setRegex($regex);
		$this->setFlags($flags);
	}

	public function setRegex($regex) {
		$this->regex = $regex;
		return $this;
	}

	public function setFlags($flags) {
		$this->flags = $flags;
		return $this;
	}

	public function registerSequence($name, $set) {
		$sequence = new CRegSequence($name, $set);

		if ($sequence->valid())
			$this->sequenceList[$sequence->name] = $sequence->set;

		return $this;
	}

	private function buildRegex() {
		return str_replace(
			array_keys($this->sequenceList),
			array_values($this->sequenceList),
			"/{$this->regex}/{$this->flags}"
		);
	}

	public function exec($subject) {
		$regex = $this->buildRegex();
		preg_match($regex, $subject, $matches);
		return $matches;
	}

	public function exec_all($subject) {
		$regex = $this->buildRegex();
		preg_match_all($regex, $subject, $matches);
		return $matches;
	}
}
