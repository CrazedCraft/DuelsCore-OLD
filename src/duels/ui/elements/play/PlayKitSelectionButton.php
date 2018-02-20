<?php

/**
 * DuelsCore – PlayKitSelectionButton.php Copyright (C) 2017 Jack Noordhuis
 *
 * This is private software, you cannot redistribute and/or modify it in any way
 * unless given explicit permission to do so. If you have not been given explicit
 * permission to view or modify this software you should take the appropriate actions
 * to remove this software from your device immediately.
 *
 * @author Jack Noordhuis
 *
 * Created on 12/9/17 at 7:36 PM
 *
 */

namespace duels\ui\elements\play;

use duels\DuelsPlayer;
use duels\kit\Kit;
use duels\Main;
use duels\ui\elements\generic\KitSelectionButton;
use pocketmine\utils\TextFormat as TF;

class PlayKitSelectionButton extends KitSelectionButton {

	/**
	 * @param bool        $value
	 * @param DuelsPlayer $player
	 */
	public function handle($value, $player) {
		if(!$player->hasDuel()) {
			if($player->hasParty() and !$player->getParty()->isOwner($player)) {
				$player->sendMessage(TF::RED . "Only the party leader can join a duel!");
			} else {
				Main::getInstance()->getDuelManager()->findDuel($player, $player->getLastSelectedDuelTypeId(), $this->getKit()->getType() === Kit::TYPE_RANDOM ? null : $this->getKit(), true);
				$player->removeLastSelectedDuelType();
			}
		} else {
			$player->sendMessage(TF::RED . "You're already in a duel!");
		}
	}

}