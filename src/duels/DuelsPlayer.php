<?php

/**
 * Duels_v1-Alpha – DuelsPlayer.php
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
 * Created on 7/8/17 at 3:18 PM
 *
 */

namespace duels;

use core\CorePlayer;
use core\Utils;
use duels\duel\Duel;
use duels\duel\DuelType;
use duels\duel\request\DuelRequestState;
use duels\kit\Kit;
use duels\party\Party;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\utils\TextFormat as TF;

class DuelsPlayer extends CorePlayer {

	/** @var string|null */
	private $lastTappedPlayerUuid = null;

	/** @var int|null */
	private $lastSelectedDuelTypeId = null;

	/** @var string|null */
	private $lastSelectedKitName = null;

	/** @var int */
	private $duelRequestState = DuelRequestState::STATE_ALL;

	/** @var array */
	private $duelRequestIds = [];

	/** @var int|null */
	private $duelId = null;

	/** @var string|null */
	private $partyId = null;

	/**
	 * Get the UUID of the last player tapped with a GUI item that
	 * requires the target (duel stick, party book, etc)
	 *
	 * @return string|null
	 */
	public function getLastTappedPlayerUuid() : ?string {
		return $this->lastTappedPlayerUuid;
	}

	/**
	 * Get the last player tapped with a GUI item that
	 * requires the target (duel stick, party book, etc)
	 *
	 * @return DuelsPlayer|CorePlayer|null
	 */
	public function getLastTappedPlayer() : ?CorePlayer {
		return Utils::getPlayerByUUID($this->lastTappedPlayerUuid ?? "");
	}

	/**
	 * Check if the player has a last tapped player
	 *
	 * @return bool
	 */
	public function hasLastTappedPlayer() : bool {
		return $this->lastTappedPlayerUuid !== null;
	}

	/**
	 * Get the last selected duel type id
	 *
	 * @return int|null
	 */
	public function getLastSelectedDuelTypeId() : ?int {
		return $this->lastSelectedDuelTypeId;
	}

	/**
	 * Get the last selected duel type
	 *
	 * @return DuelType|null
	 */
	public function getLastSelectedDuelType() : ?DuelType {
		return Main::getInstance()->getDuelManager()->getDuelType($this->lastSelectedDuelTypeId ?? -1);
	}

	/**
	 * Check if the player has a last selected duel type
	 *
	 * @return bool
	 */
	public function hasLastSelectedDuelType() : bool {
		return $this->lastSelectedDuelTypeId !== null;
	}

	/**
	 * Get the last selected kits name
	 *
	 * @return null|string
	 */
	public function getLastSelectedKitName() : ?string {
		return $this->lastSelectedKitName;
	}

	/**
	 * Get the last selected kit
	 *
	 * @return Kit|null
	 */
	public function getLastSelectedKit() : ?Kit {
		return Main::getInstance()->getKitManager()->getKit($this->lastSelectedKitName ?? null);
	}

	/**
	 * Check if the player has a last selected kit
	 *
	 * @return bool
	 */
	public function hasLastSelectedKit() : bool {
		return $this->lastSelectedKitName !== null;
	}

	/**
	 * Get the current duel request state
	 *
	 * @return int
	 */
	public function getDuelRequestState() : int {
		return $this->duelRequestState;
	}

	/**
	 * @return int[]
	 */
	public function getDuelRequestIds() : array {
		return $this->duelRequestIds;
	}

	/**
	 * @return array
	 */
	public function getDuelRequests() : array {
		$new = [];
		foreach($this->duelRequestIds as $id) {
			// TODO: Fetch duel requests from id
			$new[$id] = $id;
		}

		return $new;
	}

	/**
	 * @param int $id
	 *
	 * @return bool
	 */
	public function hasDuelRequest(int $id) : bool {
		return isset($this->duelRequestIds[$id]);
	}

	/**
	 * Get the current duel id
	 *
	 * @return int|null
	 */
	public function getDuelId() : ?int {
		return $this->duelId;
	}

	/**
	 * Get the current duel
	 *
	 * @return Duel|null
	 */
	public function getDuel() : ?Duel {
		return Main::getInstance()->getDuelManager()->getDuel($this->duelId ?? -1);
	}

	/**
	 * Check if the player has a duel
	 *
	 * @return bool
	 */
	public function hasDuel() : bool {
		return $this->duelId !== null;
	}

	/**
	 * Get the current party id
	 *
	 * @return string|null
	 */
	public function getPartyId() : ?string {
		return $this->partyId;
	}

	/**
	 * Get the current party
	 *
	 * @return Party|null
	 */
	public function getParty() {
		return Main::getInstance()->getPartyManager()->getParty($this->partyId ?? "");
	}

	/**
	 * Check if the player has a party
	 *
	 * @return bool
	 */
	public function hasParty() : bool {
		return $this->partyId !== null;
	}

	/**
	 * Set the last tapped with a GUI item that
	 * requires the target (duel stick, party book, etc)
	 *
	 * @param DuelsPlayer $player
	 */
	public function setLastTappedPlayer(DuelsPlayer $player) {
		$this->lastTappedPlayerUuid = $player->getUniqueId()->toString();
	}

	/**
	 * Forget the last player tapped with a GUI item that
	 * requires the target (duel stick, party book, etc)
	 */
	public function removeLastTappedPlayer() : void {
		$this->lastTappedPlayerUuid = null;
	}

	/**
	 * Set the last selected duel type
	 *
	 * @param DuelType $type
	 */
	public function setLastSelectedDuelType(DuelType $type) : void {
		$this->lastSelectedDuelTypeId = $type->getId();
	}

	/**
	 * Forget the last selected duel type
	 */
	public function removeLastSelectedDuelType() {
		$this->lastSelectedDuelTypeId = null;
	}

	/**
	 * Set the last selected kit
	 *
	 * @param Kit $kit
	 */
	public function setLastSelectedKit(Kit $kit) : void {
		$this->lastSelectedKitName = $kit->getName();
	}

	/**
	 * Forget the last selected kti
	 */
	public function removeLastSelectedKit() : void {
		$this->lastSelectedKitName = null;
	}

	/**
	 * Set the duel request state
	 *
	 * @param int $value
	 */
	public function setDuelRequestState(int $value) : void {
		$this->duelRequestState = $value;
	}

	/**
	 * Set the current duel
	 *
	 * @param Duel $duel
	 *
	 * WARNING: Do not call this method to add a
	 * player to a duel, use Duel::addPlayer()
	 */
	public function setDuel(Duel $duel) : void {
		$this->duelId = $duel->getArena()->getId();
	}

	/**
	 * Forget the current duel
	 *
	 * WARNING: Do not call this method to remove a
	 * player from a duel, use Duel::removePlayer()
	 */
	public function removeDuel() : void {
		$this->duelId = null;
	}

	/**
	 * Set the current party
	 *
	 * @param Party $party
	 *
	 * WARNING: Do not call this method to add a
	 * player to a party, use Party::addPlayer()
	 */
	public function setParty(Party $party) : void {
		$this->partyId = $party->getId();
	}

	/**
	 * Forget the current party
	 *
	 * WARNING: Do not call this method to remove a
	 * player from a party, use Party::removePlayer()
	 */
	public function removeParty() : void {
		$this->partyId = null;
	}

	public function attack(EntityDamageEvent $source) : void {
		parent::attack($source);

		if($source->isCancelled()) {
			Main::getInstance()->listener->onDamage($source);
		}
	}

	public function kill($forReal = false) {
		if($this->hasDuel() and $this->getState() === CorePlayer::STATE_PLAYING) {
			$duel = $this->getDuel();
			$duel->broadcast(TF::BOLD . TF::AQUA . $this->getName() . TF::RESET . TF::YELLOW . " was killed!");
			$duel->handleDeath($this);
			return;
		}

		parent::kill($forReal);
	}

}