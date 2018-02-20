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

namespace duels\ui\elements\request;

use duels\DuelsPlayer;
use duels\ui\elements\generic\KitSelectionButton;
use pocketmine\utils\TextFormat as TF;

class DuelRequestKitSelectionButton extends KitSelectionButton {

	/**
	 * @param bool $value
	 * @param DuelsPlayer $player
	 */
	public function handle($value, $player) {
		$entity = $player->getLastTappedPlayer();
		if($entity instanceof DuelsPlayer) {
			if(!$player->hasParty()) {
				$player->setLastSelectedKit($this->getKit());
				$entity->addRequest($player, $entity, $this->getKit()->getDisplayName() . " Kit");
				$player->sendMessage(TF::AQUA . "Sent a duel request to " . TF::BOLD . TF::GREEN . $entity->getName() . TF::RESET . TF::AQUA . "!");
			} else {
				$player->sendMessage(TF::RED . "You can't send a duel request whilst in a party!");
			}
		} else {
			$player->sendMessage(TF::RED . "Could not send duel request due to player being offline!");
		}
	}

}