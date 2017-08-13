<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 11/09/2016
 * Time: 6:56 PM
 */

namespace duels\stats\task;


use duels\stats\StatsDatabase;
use duels\stats\StatsRequest;
use pocketmine\Player;
use pocketmine\Server;

class CheckStatsTask extends StatsRequest {

	/** @var string */
	protected $who;

	/** @var string */
	protected $sender;

	public function __construct(StatsDatabase $database, $who, $sender) {
		parent::__construct($database->getCredentials());
		$this->who = strtolower($who);
		$this->sender = $sender;
	}

	public function onRun() {
		$mysqli = $this->getMysqli();
		$stmt = $mysqli->prepare("SELECT * FROM " . StatsDatabase::TABLE . " WHERE username = ?");
		$stmt->bind_param("s", $this->who);
		$result = $stmt->execute();
		if($result instanceof \mysqli_result) {
			$row = $result->fetch_assoc();
			if(is_array($row)) {
				$this->setResult($row);
			} else {
				$this->setResult(false);
			}
			$result->free();
		}
	}

	public function onCompletion(Server $server) {
		$sender = $server->getPlayerExact($this->sender);
		if($sender instanceof Player) {
			$result = $this->getResult();
			if(!$result) {
				$message = "";
				$sender->sendMessage($message);
			}
		}
	}

}