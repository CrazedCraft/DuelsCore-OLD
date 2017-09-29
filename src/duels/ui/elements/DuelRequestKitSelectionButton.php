<?php

/**
 * DuelsCore – DuelRequestKitSelectionButton.php
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
 * Created on 29/9/17 at 2:45 AM
 *
 */

namespace duels\ui\elements;

use core\CorePlayer;
use duels\Main;
use duels\session\PlayerSession;
use duels\ui\elements\generic\KitSelectionButton;
use pocketmine\utils\TextFormat as TF;

class DuelRequestKitSelectionButton extends KitSelectionButton {

	/**
	 * Add player to
	 *
	 * @param bool $value
	 * @param CorePlayer $player
	 */
	public function handle($value, $player) {
		$plugin = Main::getInstance();
		$pSession = $sSession = $plugin->sessionManager->get($player->getName());
		if($pSession instanceof PlayerSession) {
			$entity = $pSession->lastTapped;
			if($entity instanceof CorePlayer and $entity->isOnline()) {
				$eSession = $rSession = $plugin->sessionManager->get($entity->getName());
				if($eSession instanceof PlayerSession) {
					$pSession->lastSelectedKit = $this->getKit();
					$rSession->addRequest($player, $entity, $this->getKit()->getDisplayName() . " Kit");
					$player->sendMessage(TF::AQUA . "Sent a Duel request to " . TF::BOLD . TF::GREEN . $entity->getName() . TF::RESET . TF::AQUA . "!");
				}
			} else {
				$player->sendMessage(TF::RED . "Could not send duel request due to player being offline!");
			}
		}
	}

}