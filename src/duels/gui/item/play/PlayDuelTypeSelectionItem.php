<?php
declare(strict_types=1);

namespace duels\gui\item\play;

use core\CorePlayer;
use duels\DuelsPlayer;
use duels\gui\containers\play\PlayKitSelectionContainer;
use duels\gui\item\generic\DuelTypeSelectionItem;
use pocketmine\utils\TextFormat as TF;

class PlayDuelTypeSelectionItem extends DuelTypeSelectionItem {

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
				$player->setLastSelectedDuelType($this->getDuelType());
				$player->openGuiContainer($player->getCore()->getGuiManager()->getContainer(PlayKitSelectionContainer::CONTAINER_ID));
			}
		} else {
			$player->sendMessage(TF::RED . "You're already in a duel!");
		}

		return false;
	}

}