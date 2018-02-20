<?php

declare(strict_types=1);

namespace duels\gui\item\play;

use core\CorePlayer;
use duels\DuelsPlayer;
use duels\gui\item\generic\KitSelectionItem;
use duels\kit\Kit;
use duels\Main;
use pocketmine\utils\TextFormat as TF;

class PlayKitSelectionItem extends KitSelectionItem {

	/**
	 * @param DuelsPlayer|CorePlayer $player
	 *
	 * @return bool
	 */
	public function onSelect(CorePlayer $player) : bool {
		if(!$player->hasDuel()) {
			if($player->hasParty() and !$player->getParty()->isOwner($player)) {
				$player->sendMessage(TF::RED . "Only the party leader can join a duel!");
			} else {
				Main::getInstance()->getDuelManager()->findDuel($player, $player->getLastSelectedDuelTypeId(), $this->getKit()->getType() === Kit::TYPE_RANDOM ? null : $this->getKit(), true);
			}
		} else {
			$player->sendMessage(TF::RED . "You're already in a duel!");
		}

		return false;
	}

}