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
use pocketmine\utils\TextFormat;

class Duel {

	/** @var DuelManager */
	private $manager;

	/** @var int */
	private $countdown = self::DEFAULT_COUNTDOWN;

	/** @var int */
	private $duration = self::DEFAULT_DURATION;

	/** @var int */
	private $teamSize = self::DEFAULT_TEAM_SIZE;

	/** @var int */
	private $teamCount = self::DEFAULT_TEAM_COUNT;

	/** @var array */
	private $teams = [];

	/** @var string[] */
	private $players = [];

	/** @var string[] */
	private $spectators = [];

	/** @var bool */
	private $active = true;

	const DEFAULT_COUNTDOWN = 5; // 5 seconds
	const DEFAULT_DURATION = 900; // 15 minutes
	const DEFAULT_TEAM_SIZE = 1; // For 1v1's
	const DEFAULT_TEAM_COUNT = 2; // For 1v1's

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

	private function handleCountdown() { // Do stuff when the countdown is active
		if($this->countdown < 7) {
			switch($this->countdown) {
				case 6:
				case 5:
				case 4:
					$color = TextFormat::GOLD;
					break;
				case 3:
				case 2:
				case 1:
					$color = TextFormat::RED;
					break;
				default:
					$color = TextFormat::GREEN;
					break;
			}
			$this->broadcastMessage("{$color}Match starting in {$this->countdown}...");
			$this->broadcastTitle("{$color}Match starting in:", "{$color}{$this->countdown}...", 1, 20, 1);
		} else {
			$currentPlayers = count($this->players);
			$requiredPlayers = $this->teamCount * $this->teamSize;
			if($currentPlayers >= $requiredPlayers) {
				$this->broadcastTip(TextFormat::GREEN . "Match begins in {$this->countdown}...");
			} else {
				$this->broadcastTip(TextFormat::GREEN . "Waiting for players ({$currentPlayers}/{$requiredPlayers})");
			}
		}
		$this->countdown--;
	}

	private function handleDuration() { // Do stuff when the duel is active
		$this->duration--;
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

	/**
	 * Broadcast a popup to all players and spectators in the duel
	 *
	 * @param string $message
	 */
	public function broadcastPopup(string $message) {
		foreach(array_merge($this->players, $this->spectators) as $name => $uuid) {
			$player = Utils::getPlayerByUUID($uuid);
			if($player instanceof DuelsCorePlayer and $player->isOnline()) {
				$player->sendPopup($message);
			}
		}
	}

	/**
	 * Broadcast a tip to all players and spectators in the duel
	 *
	 * @param string $message
	 */
	public function broadcastTip(string $message) {
		foreach(array_merge($this->players, $this->spectators) as $name => $uuid) {
			$player = Utils::getPlayerByUUID($uuid);
			if($player instanceof DuelsCorePlayer and $player->isOnline()) {
				$player->sendTip($message);
			}
		}
	}

	/**
	 * Broadcast a title to all players and spectators in the duel
	 *
	 * @param string $title
	 * @param string $subtitle
	 * @param int $fadeIn
	 * @param int $stay
	 * @param int $fadeOut
	 */
	public function broadcastTitle(string $title, string $subtitle = "", int $fadeIn = -1, int $stay = -1, int $fadeOut = -1) {
		foreach(array_merge($this->players, $this->spectators) as $name => $uuid) {
			$player = Utils::getPlayerByUUID($uuid);
			if($player instanceof DuelsCorePlayer and $player->isOnline()) {
				$player->addTitle($title, $subtitle, $fadeIn, $stay, $fadeOut);
			}
		}
	}

	/**
	 * @return bool
	 */
	public function isActive() : bool {
		return $this->active;
	}

	public function __destruct() {
		$this->close();
	}

	public function close() {
		if($this->active) {
			$this->active = false;
		}
	}

}