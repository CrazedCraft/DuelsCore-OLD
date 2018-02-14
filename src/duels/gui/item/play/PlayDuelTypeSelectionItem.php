<?php

declare(strict_types=1);

namespace duels\gui\item\play;

use core\CorePlayer;
use duels\gui\containers\play\PlayKitSelectionContainer;
use duels\gui\item\generic\DuelTypeSelectionItem;
use duels\Main;
use duels\session\PlayerSession;
use pocketmine\utils\TextFormat as TF;

class PlayDuelTypeSelectionItem extends DuelTypeSelectionItem {

	public function onSelect(CorePlayer $player) : bool {
		$plugin = Main::getInstance();
		/** @var $session PlayerSession */
		if(!($session = $plugin->sessionManager->get($player->getName())) instanceof PlayerSession) {
			$player->kick(TF::RED . "Invalid session, rejoin to enjoy duels!");
		} else {
			if(!$session->inDuel()) {
				if($session->inParty() and !$session->getParty()->isOwner($player)) {
					$player->sendMessage(TF::RED . "Only the party leader can join a duel!");
				} else {
					$session->lastSelectedPlayType = $this->getDuelTypeId();
					$player->openGuiContainer($plugin->getCore()->getGuiManager()->getContainer(PlayKitSelectionContainer::CONTAINER_ID));

				}
			} else {
				$player->sendMessage(TF::RED . "You're already in a duel!");
			}
		}

		return false;
	}

}