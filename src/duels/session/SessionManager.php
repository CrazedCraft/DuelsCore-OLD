<?php

namespace duels\session;

use duels\Main;
use pocketmine\Player;

class SessionManager {

	private $plugin;

	private $sessions = [];

	private $active = true;

	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
	}

	/**
	 * @return PlayerSession[]
	 */
	public function getAll() {
		return $this->sessions;
	}

	public function add(Player $player) {
		if($this->exists($player->getName())) return;
		$this->sessions[$player->getName()] = new PlayerSession($player);
	}

	public function exists($name) {
		return isset($this->sessions[(string)$name]) and $this->sessions[(string)$name] instanceof PlayerSession;
	}

	public function remove($name) {
		if(!$this->exists($name)) return;
		$this->get($name)->close();
		unset($this->sessions[$name]);
	}

	public function get($name) {
		if(!$this->exists($name)) return;
		return $this->sessions[$name];
	}

	public function close() {
		if($this->active) {
			$this->active = false;
			foreach($this->sessions as $name => $session) {
				$this->remove($name);
			}
			unset($this->sessions);
		}
	}

	public function __destruct() {
		$this->close();
	}

}