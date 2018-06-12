<?php

/**
 * Duels_v1-Alpha – PartyInvitation.php
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
 * Created on 24/7/17 at 6:11 PM
 *
 */

namespace duels\party;

use core\CorePlayer;
use core\language\LanguageUtils;
use core\Utils;
use duels\DuelsPlayer;
use pocketmine\Player;
use pocketmine\scheduler\Task;

class PartyInvitation extends Task {

	/** @var Party */
	private $party = null;

	/** @var string */
	private $invitee = null;

	/** @var bool */
	private $accepted = false;

	public function __construct(Party $party, CorePlayer $player) {
		$this->party = $party;
		$this->invitee = $player->getName();
		$party->getManager()->getPlugin()->getScheduler()->scheduleDelayedTask($this, 20 * 60);
	}

	/**
	 * @return Party
	 */
	public function getParty() : Party {
		return $this->party;
	}

	/**
	 * @return null|DuelsPlayer|Player
	 */
	public function getInvitee() {
		return $this->party->getManager()->getPlugin()->getServer()->getPlayerExact($this->invitee);
	}

	/**
	 * @return bool
	 */
	public function hasBeenAccepted() : bool {
		return $this->accepted;
	}

	/**
	 * @param bool $value
	 */
	public function setAccepted(bool $value) {
		$this->accepted = $value;
	}

	public function onRun($currentTick) {
		if(!$this->accepted) {
			$this->accepted = true;
			$this->party->broadcastMessage(Utils::translateColors("&c- &6Party invitation to {$this->invitee} has expired!"));
			$this->party->removeInvitation($this->invitee);

			$invitee = $this->getInvitee();
			if($invitee instanceof DuelsPlayer) {
				$invitee->sendMessage(LanguageUtils::translateColors("&c- &6Party invitation from {$this->party->getOwner()->getName()} has expired!"));
			}
		}
	}

	public function onCancel() {
		$this->party->removeInvitation($this->invitee);
	}

}