<?php

namespace duels\stats;

use core\database\mysql\MySQLRequest;

abstract class StatsRequest extends MySQLRequest {

	/* The key used to store a mysqli instance onto the thread */
	const STATS_KEY = "mysqli.stats";

	/**
	 * @return mixed|\mysqli
	 */
	public function getMysqli() {
		$mysqli = $this->getFromThreadStore(self::STATS_KEY);
		if($mysqli !== null){
			return $mysqli;
		}
		$mysqli = parent::getMysqli();
		$this->saveToThreadStore(self::STATS_KEY, $mysqli);
		return $mysqli;
	}

}