<?php

/**
 * DuelsCore – DuelsCore.php
 *
 * Copyright (C) 2017 Jack Noordhuis
 *
 * This is private software, you cannot redistribute and/or modify it in any way
 * unless given explicit permission to do so. If you have not been given explicit
 * permission to view or modify this software you should take the appropriate actions
 * to remove this software from your device immediately.
 *
 * @author JackNoordhuis
 *
 * Created on 24/5/17 at 8:53 PM
 *
 */

namespace duelscore;

use duelscore\duel\DuelManager;
use pocketmine\plugin\PluginBase;

class DuelsCore extends PluginBase {

	/** @var DuelManager */
	private $duelManager;

	public function onEnable() {
		$this->setDuelManager();
	}

	public function getDuelManager() : DuelManager {
		return $this->duelManager;
	}

	public function setDuelManager() {
		$this->duelManager = new DuelManager($this);
	}

}