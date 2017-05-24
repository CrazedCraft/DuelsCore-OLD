<?php

/**
 * DuelsCore – Duel.php
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
 * Created on 24/5/17 at 9:14 PM
 *
 */

namespace duelscore\duel;

use core\Utils;
use duelscore\DuelsCorePlayer;

class Duel {

	/** @var DuelManager */
	private $manager;

	/** @var int */
	private $countdown = 15; // 15 seconds

	/** @var int */
	private $duration = 900; // 15 minutes

	/** @var string[] */
	private $players = [];

	/** @var string[] */
	private $spectators = [];

	public function __construct(DuelManager $manager) {
		$this->manager = $manager;
	}

	public function tick() {
		if($this->countdown > 0) {
			$this->handleCountdown();
		} else {
			$this->handleDuration();
		}
	}

	public function handleCountdown() {

	}

	public function handleDuration() {

	}

	/**
	 * Check if a player is in the duel using a player
	 *
	 * @param DuelsCorePlayer $player
	 *
	 * @return bool
	 */
	public function inDuelAsPlayerByPlayer(DuelsCorePlayer $player) {
		return isset($this->players[$player->getName()]) and $player->getUniqueId()->toString() === $this->players[$player->getName()];
	}

	/**
	 * Check if a player is in the duel by using their name
	 *
	 * @param string $name
	 * @param bool $findPlayer
	 *
	 * @return bool
	 */
	public function inDuelAsPlayerByName(string $name, bool $findPlayer = false) {
		if($findPlayer) {
			$target = $this->manager->getPlugin()->getServer()->getPlayerExact($name);
			if($target instanceof DuelsCorePlayer and $target->isOnline()) {
				return isset($this->players[$target->getName()]) and $target->getUniqueId()->toString() === $this->players[$target->getName()];
			}
		}
		return isset($this->players[$name]);
	}

	/**
	 * Add a player to the duel
	 *
	 * @param DuelsCorePlayer $player
	 */
	public function addPlayer(DuelsCorePlayer $player) {
		$name = $player->getName();
		if(!isset($this->players[$name])) {
			$this->players[$name] = $player->getUniqueId()->toString();
		}
	}

	/**
	 * Remove a player from the duel using a player
	 *
	 * @param DuelsCorePlayer $player
	 */
	public function removePlayerByPlayer(DuelsCorePlayer $player) {
		if($this->inDuelAsPlayerByPlayer($player)) {
			unset($this->players[$player->getName()]);
		}
	}

	/**
	 * Remove a player from the duel using their name
	 *
	 * @param string $name
	 */
	public function removePlayerByName(string $name) {
		if($this->inDuelAsPlayerByName($name, false)) {
			unset($this->players[$name]);
		}
	}

	/**
	 * Check if a player is spectating the duel using a player
	 *
	 * @param DuelsCorePlayer $player
	 *
	 * @return bool
	 */
	public function inDuelAsSpectatorByPlayer(DuelsCorePlayer $player) {
		return isset($this->players[$player->getName()]) and $player->getUniqueId()->toString() === $this->players[$player->getName()];
	}

	/**
	 * Check if a player is spectating the duel by using their name
	 *
	 * @param string $name
	 * @param bool $findPlayer
	 *
	 * @return bool
	 */
	public function inDuelAsSpectatorByName(string $name, bool $findPlayer = false) {
		if($findPlayer) {
			$target = $this->manager->getPlugin()->getServer()->getPlayerExact($name);
			if($target instanceof DuelsCorePlayer and $target->isOnline()) {
				return isset($this->players[$target->getName()]) and $target->getUniqueId()->toString() === $this->players[$target->getName()];
			}
		}
		return isset($this->players[$name]);
	}

	/**
	 * Add a spectator to the duel
	 *
	 * @param DuelsCorePlayer $player
	 */
	public function addSpectator(DuelsCorePlayer $player) {
		$name = $player->getName();
		if(!isset($this->players[$name])) {
			$this->players[$name] = $player->getUniqueId()->toString();
		}
	}

	/**
	 * Remove a spectating player from the duel using a player
	 *
	 * @param DuelsCorePlayer $player
	 */
	public function removeSpectatorByPlayer(DuelsCorePlayer $player) {
		if($this->inDuelAsSpectatorByPlayer($player)) {
			unset($this->players[$player->getName()]);
		}
	}

	/**
	 * Remove a spectating player from the duel using their name
	 *
	 * @param string $name
	 */
	public function removeSpectatorByName(string $name) {
		if($this->inDuelAsSpectatorByName($name, false)) {
			unset($this->players[$name]);
		}
	}

	/**
	 * Broadcast a message to all players and spectators in the duel
	 *
	 * @param string $message
	 */
	public function broadcastMessage(string $message) {
		foreach(array_merge($this->players, $this->spectators) as $name => $uuid) {
			$player = Utils::getPlayerByUUID($uuid);
			if($player instanceof DuelsCorePlayer and $player->isOnline()) {
				$player->sendMessage($message);
			}
		}
	}

}