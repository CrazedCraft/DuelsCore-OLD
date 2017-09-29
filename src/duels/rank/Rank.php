<?php

namespace duels\rank;

use duels\Main;

class Rank {

	private $name;

	private $format = "";

	private $perms = [];

	public function __construct($name, $format, array $perms = []) {
		$this->name = $name;
		$this->format = $format;
		$this->perms = $perms;
	}

	public function getFormat() {
		return Main::translateColors($this->format);
	}

	public function addPerm($node) {
		$this->perms[] = $node;
	}

	public function getPerms() {
		return $this->perms;
	}

}
