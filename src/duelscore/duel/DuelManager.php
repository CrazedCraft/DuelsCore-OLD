<?php

/**
 * DuelsCore – DuelManager.php
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
 * Created on 24/5/17 at 9:04 PM
 *
 */

namespace duelscore\duel;

use duelscore\DuelsCore;

class DuelManager {

	/** @var DuelsCore */
	private $plugin;

	/** @var array */
	private $duels = [];

	/** @var bool */
	private $closed = false;

	public function __construct(DuelsCore $plugin) {
		$this->plugin = $plugin;
	}

	/**
	 * @return DuelsCore
	 */
	public function getPlugin() : DuelsCore {
		return $this->plugin;
	}

	/**
	 * Tick all active duels and remove all inactive ones
	 */
	public function tickDuels() {
		foreach($this->duels as $duel) {

		}
	}

	public function __destruct() {
		$this->close();
	}

	public function close() {
		if(!$this->closed) {

		}
	}

}