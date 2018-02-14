<?php

declare(strict_types=1);

namespace duels\gui\item\play;

use core\CorePlayer;
use duels\gui\item\generic\KitSelectionItem;
use duels\kit\Kit;
use duels\Main;
use duels\session\PlayerSession;
use pocketmine\utils\TextFormat as TF;

class PlayKitSelectionItem extends KitSelectionItem {

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
					Main::getInstance()->getDuelManager()->findDuel($player, $session->lastSelectedPlayType, $this->getKit()->getType() === Kit::TYPE_RANDOM ? null : $this->getKit(), true);
				}
			} else {
				$player->sendMessage(TF::RED . "You're already in a duel!");
			}
		}

		return false;
	}

}