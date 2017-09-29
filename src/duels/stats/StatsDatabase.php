<?php

namespace duels\stats;

use core\database\mysql\MySQLCredentials;
use duels\Main;
use duels\stats\task\RegisterTask;

class StatsDatabase {

	/** @var Main */
	private $plugin;

	/** @var MySQLCredentials */
	private $credentials;

	/* Constants */
	const TABLE = "duels_stats";
	const USERNAME = "username";
	const DIAMOND_WINS = "diamond_wins";
	const DIAMOND_KILLS = "diamond_kills";
	const DIAMOND_LOSES = "diamond_loses";
	const SG_WINS = "sg_wins";
	const SG_KILLS = "sg_kills";
	const SG_LOSES = "sg_loses";
	const IRONSOUP_WINS = "ironsoup_wins";
	const IRONSOUP_KILLS = "ironsoup_kills";
	const IRONSOUP_LOSES = "ironsoup_loses";
	const BASIC_WINS = "basic_wins";
	const BASIC_KILLS = "basic_kills";
	const BASIC_LOSES = "basic_loses";
	const ASSASSIN_WINS = "assassin_wins";
	const ASSASSIN_KILLS = "assassin_kills";
	const ASSASSIN_LOSES = "assassin_loses";

	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
		$this->credentials = MySQLCredentials::fromArray([
			"host" => "127.0.0.1",
			"user" => "root",
			"password" => "L8332s58050F92j",
			"name" => "crazedcraft",
			"port" => 3306
		]);
	}

	/**
	 * @return MySQLCredentials
	 */
	public function getCredentials() {
		return $this->credentials;
	}

	public function register($name) {
		$this->plugin->getServer()->getScheduler()->scheduleAsyncTask(new RegisterTask($this, $name));
	}

	public function update($name, $type = self::DIAMOND_WINS) {
		$this->plugin->getServer()->getScheduler()->scheduleAsyncTask(new RegisterTask($this, $name, $type));
	}

	public function sendStatsInfo($name, $sender) {

	}

	public function reset($name, $type) {

	}

	public function unregister($name) {

	}

}