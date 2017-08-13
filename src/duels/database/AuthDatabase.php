<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace duels\database;

use duels\Main;
use pocketmine\Player;

class AuthDatabase extends Database {

	private $plugin;

	private $table;

	private $host;

	private $user;

	private $password;

	private $name;

	private $port;

	private $database;

	public function __construct(Main $plugin, $host, $user, $password, $database, $port = 3306) {
		parent::__construct($plugin, "crazedcraft_auth", $host, $user, $password, $database, $port);
		$this->plugin = $plugin;
		$this->table = "crazedcraft_auth";
		$this->host = $host;
		$this->user = $user;
		$this->password = $password;
		$this->name = $database;
		$this->port = $port;
		$this->database = new \mysqli($this->host, $this->user, $this->password, $this->name, $this->port);
		if($this->database->connect_error) $plugin->getServer()->shutdown();
		$defaults = $plugin->getResource("mysql.sql");
		$this->database->query(stream_get_contents($defaults));
		fclose($defaults);

	}

	public function register(Player $player, $password, $email = "") {
		$key = trim(strtolower($player->getName()));

		$this->database->query("INSERT INTO crazedcraft_auth
                        (username, rank, colors, email, password, lastip, uuid, lastlogin, registerdate)
                        VALUES
                        ('" . $this->database->escape_string($key) . "', '', '', '" . $email . "', '" . $password . "', '" . $player->getAddress() . "', '" . $player->getUniqueId() . "', '" . time() . "', '" . time() . "')");
	}

	public function resetPass($name, $pass) {
		$key = trim(strtolower($name));
		$this->database->query("UPDATE " . $this->table . " SET password = '" . $pass . "' WHERE username = '" . $key . "'");
	}

	public function setEmail($name, $email) {
		$key = trim(strtolower($name));
		$this->database->query("UPDATE " . $this->table . " SET email = '" . $email . "' WHERE username = '" . $key . "'");
	}

	public function setRank($name, $string) {
		$key = trim(strtolower($name));
		$this->database->query("UPDATE crazedcraft_auth SET rank = '" . $string . "' WHERE username = '" . $key . "'");
	}

	public function removeRank($name, $string) {
		$key = trim(strtolower($name));
		$temp = explode(",", str_replace(" ", "", $this->get($name)["rank"]));
		$ranks = "";
		foreach($temp as $key => $rank) {
			if(strpos($string, $rank) !== false) {
				unset($temp[$key]);
			} else {
				$ranks .= ($ranks === "" ? $rank : ", " . $rank);
			}
		}
		$this->database->query("UPDATE crazedcraft_auth SET rank = '" . $ranks . "' WHERE username = '" . $key . "'");
	}

	public function resetRank($name) {
		$key = trim(strtolower($name));
		$this->database->query("UPDATE crazedcraft_auth SET rank = '' WHERE username = '" . $key . "'");

	}

	public function getRanks($name) {
		return $this->get($name)["rank"];
	}

	public function resetAllRanks($code) {
		if($code === "JACKISBAE") {
			$this->database->query("UPDATE crazedcraft_auth SET rank = ''");
		}
	}

}
