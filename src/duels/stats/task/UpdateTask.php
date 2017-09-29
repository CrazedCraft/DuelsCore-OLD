<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 11/09/2016
 * Time: 6:46 PM
 */

namespace duels\stats\task;


use duels\stats\StatsDatabase;
use duels\stats\StatsRequest;

class UpdateTask extends StatsRequest {

	/** @var string */
	protected $name;

	/** @var string */
	protected $type;

	public function __construct(StatsDatabase $database, $name, $type) {
		parent::__construct($database->getCredentials());
		$this->name = strtolower($name);
		$this->type = $type;
	}

	public function onRun() {
		$mysqli = $this->getMysqli();
		$stmt = $mysqli->prepare("UPDATE " . StatsDatabase::TABLE . " SET " . $this->type . " " . $this->type . " + 1 WHERE username = ?");
		$stmt->bind_param("s", $this->name);
		$stmt->execute();
	}

}