<?php

declare(strict_types=1);

namespace duels\gui\item\request;

use core\CorePlayer;
use duels\gui\containers\request\DuelRequestDuelTypeSelectionContainer;
use duels\gui\item\generic\DuelTypeSelectionItem;
use duels\Main;
use duels\session\PlayerSession;
use pocketmine\utils\TextFormat as TF;

class DuelRequestDuelTypeSelectionItem extends DuelTypeSelectionItem {

	public function onSelect(CorePlayer $player) : bool {
		$plugin = Main::getInstance();
		/** @var $session PlayerSession */
		if(!($session = $plugin->sessionManager->get($player->getName())) instanceof PlayerSession) {
			$player->kick(TF::RED . "Invalid session, rejoin to enjoy duels!");
		} else {
			if(!$session->inDuel()) {
				if($session->inParty()) {
					$player->sendMessage(TF::RED . "You can't send a duel request whilst in a party!");
				} else {
					$session->lastSelectedPlayType = $this->getDuelTypeId();
					$player->openGuiContainer($plugin->getCore()->getGuiManager()->getContainer(DuelRequestDuelTypeSelectionContainer::CONTAINER_ID));
				}
			} else {
				$player->sendMessage(TF::RED . "You're already in a duel!");
			}
		}

		return false;
	}

}