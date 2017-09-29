<?php

namespace duels\arena;

use pocketmine\math\Vector3;

class Arena {

	/** @var bool */
	public $inUse = false;

	/** @var int */
	private $id = -1;

	/** @var string */
	private $creator;

	/** @var Vector3[] */
	private $locations;

	public function __construct(int $id, string $creator, array $locations) {
		$this->id = $id;
		$this->creator = $creator;
		$this->locations = $locations;
	}

	public function getId() : int {
		return $this->id;
	}

	public function getCreator() : string {
		return $this->creator;
	}

	public function getLocations() : array {
		return $this->locations;
	}

}