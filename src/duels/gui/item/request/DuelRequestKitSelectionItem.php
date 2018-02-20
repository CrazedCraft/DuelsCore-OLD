<?php

declare(strict_types=1);

namespace duels\gui\item\request;

use core\CorePlayer;
use duels\DuelsPlayer;
use duels\gui\item\generic\KitSelectionItem;
use duels\Main;
use pocketmine\utils\TextFormat as TF;

class DuelRequestKitSelectionItem extends KitSelectionItem {

	/**
	 * @param DuelsPlayer|CorePlayer $player
	 *
	 * @return bool
	 */
	public function onSelect(CorePlayer $player) : bool {
		$entity = $player->getLastTappedPlayer();
		if($entity instanceof DuelsPlayer) {
			$player->setLastSelectedKit($this->getKit());
			$entity->addRequest($player, $entity, $this->getKit()->getDisplayName() . " Kit");
			$player->sendMessage(TF::AQUA . "Sent a Duel request to " . TF::BOLD . TF::GREEN . $entity->getName() . TF::RESET . TF::AQUA . "!");
		} else {
			$player->sendMessage(TF::RED . "Could not send duel request due to player being offline!");
		}

		return false;
	}

}