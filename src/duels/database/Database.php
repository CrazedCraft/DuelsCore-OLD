<?php

namespace duels\database;

use duels\Main;
use pocketmine\Player;

class Database {

	private $plugin;

	private $table;

	private $host;

	private $user;

	private $password;

	private $name;

	private $port;

	private $database;

	public function __construct(Main $plugin, $table, $host, $user, $password, $database, $port = 3306) {
		$this->plugin = $plugin;
		$this->table = $table;
		$this->host = $host;
		$this->user = $user;
		$this->password = $password;
		$this->name = $database;
		$this->port = $port;
		$this->connect();
		$defaults = $plugin->getResource("mysql.sql");
		$this->database->query(stream_get_contents($defaults));
		fclose($defaults);
	}

	public function connect() {
		if($this->connected()) return;
		$this->database = new \mysqli($this->host, $this->user, $this->password, $this->name, $this->port);
		if($this->database->connect_error) {
			return false;
		}
	}

	public function connected() {
		return isset($this->database) and !$this->database->connect_error;
	}

	public function add(Player $player) {

	}

	public function exists($name) {
		return $this->get($name) !== null;
	}

	public function get($name) {
		if(!$this->connected()) return null;
		$key = trim(strtolower($name));

		$result = $this->database->query("SELECT * FROM " . $this->table . " WHERE username = '" . $this->database->escape_string($key) . "'");

		if($result instanceof \mysqli_result) {
			$data = $result->fetch_assoc();
			$result->free();
			return $data;
		}
		return null;
	}

	public function delete($name) {
		$key = trim(strtolower($name));
		$this->database->query("DELETE FROM " . $this->table . " WHERE name = '" . $key . "'");
	}
}
