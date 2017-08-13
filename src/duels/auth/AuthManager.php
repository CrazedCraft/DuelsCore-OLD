<?php

namespace duels\auth;

use duels\Main;

class AuthManager {

	private $needAuth = [];

	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
	}

	public function authenticate(Player $player) {

	}

	public function isAuthenticated($name) {
		return !isset($this->needAuth[$name]);
	}

	public function unAuthenticate($name) {
		$this->needAuth[$name];
	}

}
