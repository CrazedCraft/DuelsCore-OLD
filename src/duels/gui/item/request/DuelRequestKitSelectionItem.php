<?php

declare(strict_types=1);

namespace duels\gui\item\request;

use core\CorePlayer;
use duels\gui\item\generic\KitSelectionItem;
use duels\Main;
use duels\session\PlayerSession;
use pocketmine\utils\TextFormat as TF;

class DuelRequestKitSelectionItem extends KitSelectionItem {

	/**
	 * @param CorePlayer $player
	 *
	 * @return bool
	 */
	public function onSelect(CorePlayer $player) : bool {
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

		return false;
	}

}