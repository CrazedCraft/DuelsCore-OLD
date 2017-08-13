<?php

namespace duels\stats\task;

use duels\stats\StatsDatabase;
use duels\stats\StatsRequest;

class RegisterTask extends StatsRequest {

	/** @var string */
	protected $name;

	public function __construct(StatsDatabase $database, $name) {
		parent::__construct($database->getCredentials());
		$this->name = strtolower($name);
	}

	public function onRun() {
		$mysqli = $this->getMysqli();
		$stmt = $mysqli->prepare("INSERT INTO " . StatsDatabase::TABLE . " (username) VALUES
			(?)");
		$stmt->bind_param("s", $this->name);
		$stmt->execute();
	}

}