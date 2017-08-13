<?php

/**
 * DuelsCore – ArenaManager.php
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
 * Created on 7/7/17 at 5:55 PM
 *
 */

namespace duelscore\arena;

use duelscore\DuelsCore;

class ArenaManager {

	/** @var DuelsCore */
	private $plugin;

	/** @var Arena[] */
	private $arenas = [];

	public function __construct(DuelsCore $plugin) {
		$this->plugin = $plugin;
	}

	/**
	 * @return DuelsCore
	 */
	public function getPlugin() : DuelsCore {
		return $this->plugin;
	}

}