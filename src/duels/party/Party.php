<?php

/**
 * Duels_v1-Alpha – Party.php
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

use core\CorePlayer;
use core\Utils;
use duels\session\PlayerSession;
use pocketmine\Player;

class Party {

	/** @var PartyManager */
	private $manager = null;

	/** @var string */
	private $id = null;

	/** @var PartyInvitation[] */
	private $invitations = [];

	/** @var string */
	private $owner = null;

	/** @var string[] */
	private $players = [];

	public function __construct(PartyManager $manager) {
		$this->manager = $manager;
		$this->id = md5(spl_object_hash($this));
	}

	public function getManager() : PartyManager {
		return $this->manager;
	}

	/**
	 * @return string
	 */
	public function getId() : string {
		return $this->id;
	}

	/**
	 * @param Player $owner
	 */
	public function setOwner(Player $owner) {
		$this->owner = $owner->getName();
	}

	/**
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function isOwner(Player $player) {
		return $this->owner === $player->getName();
	}

	/**
	 * @return Player
	 */
	public function getOwner() {
		return $this->manager->getPlugin()->getServer()->getPlayerExact($this->owner);
	}

	/**
	 * @param CorePlayer $player
	 * @param bool $broadcast
	 */
	public function addPlayer(CorePlayer $player, bool $broadcast = true) {
		/** @var $session PlayerSession */
		if(($session = $this->getManager()->getPlugin()->sessionManager->get($player->getName())) instanceof PlayerSession) {
			$session->setParty($this);
			if($broadcast)
				$this->broadcastMessage(Utils::translateColors("&6- &a{$player->getName()} has joined the party!"));
			$this->players[$player->getName()] = $player->getUniqueId()->toString();
			if($broadcast)
				$player->sendMessage(Utils::translateColors("&6- &aYou have joined {$this->owner}('s) party!"));
		}
	}

	/**
	 * @return array
	 */
	public function getPlayers() : array {
		return $this->players;
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function inParty(string $name) : bool {
		if(isset($this->players[$name]))
			return true;
		foreach($this->players as $p => $uid) {
			if(strtolower($name) === strtolower($p))
				return true;
		}
		return false;
	}

	/**
	 * Check to make sure there are at least two players in a party
	 */
	public function checkPlayers() {
		if(count($this->players) <= 1) {
			$this->disband("no other players in party");
		}
	}

	/**
	 * @param string $name
	 * @param bool $broadcast
	 */
	public function removePlayer(string $name, bool $broadcast = true) {
		if(isset($this->players[$name])) {
			unset($this->players[$name]);
			if($broadcast)
				$this->broadcastMessage(Utils::translateColors("&c- &6{$name} has left the party!"));
			/** @var $session PlayerSession */
			if(($session = $this->getManager()->getPlugin()->getSessionManager()->get($name)) instanceof PlayerSession) {
				$session->removeParty();
			}
			$this->checkPlayers();
		}
	}

	/**
	 * @param string $name
	 * @param bool $broadcast
	 */
	public function kickPlayer(string $name, bool $broadcast = true) {
		if(isset($this->players[$name])) {
			if(isset($this->players[$name])) {
				unset($this->players[$name]);
			} else {
				$found = false;
				foreach($this->players as $p => $uid) {
					if(strtolower($name) === strtolower($p)) {
						$found = true;
						unset($this->players[$p]);
						break;
					}
				}
				if(!$found) return;
			}
			if($broadcast)
				$this->broadcastMessage(Utils::translateColors("&c- &6{$name} was removed from the party!"));
			/** @var $session PlayerSession */
			if(($session = $this->getManager()->getPlugin()->sessionManager->get($name)) instanceof PlayerSession) {
				$session->removeParty();
			}
			$this->checkPlayers();
		}
	}

	/**
	 * @param CorePlayer $player
	 */
	public function invitePlayer(CorePlayer $player) {
		$this->invitations[$player->getName()] = new PartyInvitation($this, $player);
		$player->sendMessage(Utils::translateColors("&6- &a{$this->owner} has invited you to their party! Do &o/party accept {$this->owner}&r&a within 60 seconds to join their party!"));
		$this->broadcastMessage(Utils::translateColors("&6- &a{$player->getName()} has been invited to the party, they have 60 seconds to accept!"));
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function hasInvitation(string $name) {
		return isset($this->invitations[$name]) and $this->invitations[$name] instanceof PartyInvitation;
	}

	/**
	 * @param CorePlayer $player
	 *
	 * @return bool
	 */
	public function acceptInvitation(CorePlayer $player) {
		if($this->hasInvitation($player->getName())) {
			$invitation = $this->invitations[$player->getName()];
			$this->addPlayer($player);
			$invitation->setAccepted(true);
			$this->manager->getPlugin()->getServer()->getScheduler()->cancelTask($invitation->getTaskId());
			return true;
		}
		return false;
	}

	/**
	 * @param string $name
	 */
	public function removeInvitation(string $name) {
		if(!$this->hasInvitation($name)) return;
		unset($this->invitations[$name]);
		$this->checkPlayers();
	}

	/**
	 * @param string $reason
	 */
	public function disband(string $reason) {
		$this->broadcastMessage(Utils::translateColors("&c- &7The party has been disbanded due to '{$reason}'"));
		foreach($this->players as $p => $uid) {
			$this->removePlayer($p, false);
		}
		$this->manager->removeParty($this->id);
	}

	/**
	 * Send a list of players in the party to a player
	 *
	 * @param CorePlayer $player
	 */
	public function sendList(CorePlayer $player) {
		$player->sendMessage(Utils::translateColors("&aPlayers in your party &7(" . count($this->players) . ")&a:\n&r&e" . implode("&7, &e", array_keys($this->players))));
	}

	/**
	 * @param string $message
	 */
	public function broadcastMessage(string $message) {
		foreach($this->players as $p) {
			if(($p = Utils::getPlayerByUUID($p)) instanceof Player) {
				$p->sendMessage($message);
			}
		}
	}

}