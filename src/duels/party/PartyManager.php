<?php

/**
 * Duels_v1-Alpha – PartyManager.php
 *
 * Copyright (C) 2017 Jack Noordhuis
 *
 * This is private software, you cannot redistribute and/or modify it in any way
 * unless given explicit permission to do so. If you have not been given explicit
 * permission to view or modify this software you should take the appropriate actions
 * to remove this software from your device immediately.
 *
 * @author Jack Noordhuis
 *
 * Created on 24/7/17 at 6:03 PM
 *
 */

namespace duels\party;

use duels\Main;

class PartyManager {

	/** @var Main */
	private $plugin = null;

	/** @var Party[] */
	private $parties = [];

	/** @var bool */
	private $closed = false;

	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
	}

	public function getPlugin() : Main {
		return $this->plugin;
	}

	/**
	 * @param Party $party
	 */
	public function addParty(Party $party) {
		$this->parties[$party->getId()] = $party;
	}

	/**
	 * @param string $id
	 *
	 * @return Party|null
	 */
	public function getParty(string $id) {
		return $this->parties[$id] ?? null;
	}

	/**
	 * @param string $id
	 */
	public function removeParty(string $id) {
		unset($this->parties[$id]);
	}

	public function close() {
		if(!$this->closed) {
			foreach($this->parties as $p) {
				$p->disband("server shutting down");
			}
		}
	}

	public function __destruct() {
		$this->close();
	}

}