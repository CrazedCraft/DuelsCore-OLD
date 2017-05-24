<?php

/**
 * DuelsCore – DuelsCoreListener.php
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
 * Created on 24/5/17 at 8:58 PM
 *
 */

namespace duelscore;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCreationEvent;

class DuelsCoreListener implements Listener {

	/** @var DuelsCore */
	private $plugin;

	public function __construct(DuelsCore $plugin) {
		$this->plugin = $plugin;
		$plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
	}

	/**
	 * @return DuelsCore
	 */
	public function getPlugin() : DuelsCore {
		return $this->plugin;
	}

	/**
	 * Make sure our custom duel player is used
	 *
	 * @param PlayerCreationEvent $event
	 *
	 * @priority HIGHEST
	 */
	public function onPlayerCreation(PlayerCreationEvent $event) {
		$event->setPlayerClass(DuelsCorePlayer::class);
	}

}