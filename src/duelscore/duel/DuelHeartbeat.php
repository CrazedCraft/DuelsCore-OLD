<?php

/**
 * DuelsCore – DuelHeartbeat.php
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
 * Created on 24/5/17 at 9:09 PM
 *
 */

namespace duelscore\duel;

use duelscore\DuelsCore;
use pocketmine\scheduler\PluginTask;

class DuelHeartbeat extends PluginTask {

	/** @var bool */
	private $active = true;

	/**
	 * DuelHeartbeat constructor.
	 *
	 * @param DuelsCore $plugin
	 */
	public function __construct(DuelsCore $plugin) {
		parent::__construct($plugin);
		$this->setHandler($plugin->getServer()->getScheduler()->scheduleRepeatingTask($this, 1));
	}

	/**
	 * @return bool
	 */
	public function isActive() {
		return $this->active;
	}

	/**
	 * Runs all the duel things on all the active duels
	 *
	 * @param int $tick
	 */
	public function onRun($tick) {
		/** @var DuelsCore $plugin */
		$plugin = $this->getOwner();
		$plugin->getDuelManager()->tickDuels();
	}

	public function onCancel() {
		$this->close();
	}

	public function close() {
		if($this->active) {
			$this->active = false;
			$this->getOwner()->getServer()->getScheduler()->cancelTask($this->getTaskId());
		}
	}

}