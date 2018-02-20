<?php

declare(strict_types=1);

namespace duels\gui\item\request;

use core\CorePlayer;
use duels\DuelsPlayer;
use duels\gui\containers\request\DuelRequestDuelTypeSelectionContainer;
use duels\gui\item\generic\DuelTypeSelectionItem;
use pocketmine\utils\TextFormat as TF;

class DuelRequestDuelTypeSelectionItem extends DuelTypeSelectionItem {

	/**
	 * @param DuelsPlayer|CorePlayer $player
	 *
	 * @return bool
	 */
	public function onSelect(CorePlayer $player) : bool {
		if(!$player->hasDuel()) {
			if($player->hasParty()) {
				$player->sendMessage(TF::RED . "You can't send a duel request whilst in a party!");
			} else {
				$player->setLastSelectedDuelType($this->getDuelType());
				$player->openGuiContainer($player->getCore()->getGuiManager()->getContainer(DuelRequestDuelTypeSelectionContainer::CONTAINER_ID));
			}
		} else {
			$player->sendMessage(TF::RED . "You're already in a duel!");
		}

		return false;
	}

}