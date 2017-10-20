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
use duels\session\PlayerSession;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\utils\TextFormat as TF;

class DuelsPlayer extends CorePlayer {
	//
	///** @var string */
	//private $lastTappedPLayerUuid = "";
	//
	///** @var int */
	//private $lastSelectedKitId = -1;
	//
	///** @var bool */
	//private $requestStatus = true;
	//
	///** @var array */
	//private $requestIds = [];
	//
	///** @var int */
	//private $duelId = -1;
	//
	///** @var int */
	//private $lasSelectedPartyType = -1;

	public function attack($damage, EntityDamageEvent $source) {
		$v = parent::attack($damage, $source);

		if($source->isCancelled()) {
			Main::getInstance()->listener->onDamage($source);
		}
		return $v;
	}

	public function kill($forReal = false) {
		$plugin = Main::getInstance();
		$session = $plugin->getSessionManager()->get($this->getName());
		if($session instanceof PlayerSession) {
			if($session->inDuel() and $session->getStatus() === PlayerSession::STATUS_PLAYING and $this->getState() === CorePlayer::STATE_PLAYING) {
				$session->getDuel()->broadcast(TF::BOLD . TF::AQUA . $this->getName() . TF::RESET . TF::YELLOW . " was killed!");
				$session->getDuel()->handleDeath($this);
				return;
			}
		}

		parent::kill($forReal);
	}

}