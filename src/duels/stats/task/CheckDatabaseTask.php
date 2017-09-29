<?php

namespace duels\stats\task;

use duels\stats\StatsDatabase;
use duels\stats\StatsRequest;

class CheckDatabaseTask extends StatsRequest {

	public function __construct(StatsDatabase $database) {
		parent::__construct($database->getCredentials());
	}

	public function onRun() {
		$mysqli = $this->getMysqli();
		$mysqli->query("CREATE TABLE IF NOT EXISTS " . StatsDatabase::TABLE . " (
			" . StatsDatabase::USERNAME . " VARCHAR(64) PRIMARY KEY,
			" . StatsDatabase::DIAMOND_WINS . " INT DEFAULT 0,
			" . StatsDatabase::DIAMOND_KILLS . " INT DEFAULT 0,
			" . StatsDatabase::DIAMOND_LOSES . " INT DEFAULT 0,
			" . StatsDatabase::SG_WINS . " INT DEFAULT 0,
			" . StatsDatabase::SG_KILLS . " INT DEFAULT 0,
			" . StatsDatabase::SG_LOSES . " INT DEFAULT 0,
			" . StatsDatabase::IRONSOUP_WINS . " INT DEFAULT 0,
			" . StatsDatabase::IRONSOUP_KILLS . " INT DEFAULT 0,
			" . StatsDatabase::IRONSOUP_LOSES . " INT DEFAULT 0,
			" . StatsDatabase::BASIC_WINS . " INT DEFAULT 0,
			" . StatsDatabase::BASIC_KILLS . " INT DEFAULT 0,
			" . StatsDatabase::BASIC_LOSES . " INT DEFAULT 0,
			" . StatsDatabase::ASSASSIN_WINS . " INT DEFAULT 0,
			" . StatsDatabase::ASSASSIN_KILLS . " INT DEFAULT 0,
			" . StatsDatabase::ASSASSIN_LOSES . " INT DEFAULT 0
		)");
	}

}