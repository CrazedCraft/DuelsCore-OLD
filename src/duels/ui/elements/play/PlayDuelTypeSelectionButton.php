<?php

declare(strict_types=1);

namespace duels\ui\elements\play;

use duels\DuelsPlayer;
use duels\Main;
use duels\session\PlayerSession;
use duels\ui\elements\generic\DuelTypeSelectionButton;
use duels\ui\windows\play\PlayKitSelectionForm;
use pocketmine\utils\TextFormat as TF;

class PlayDuelTypeSelectionButton extends DuelTypeSelectionButton {

	/**
	 * @param $value
	 * @param DuelsPlayer $player
	 */
	public function handle($value, $player) {
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
					$player->showModal($plugin->getCore()->getUiManager()->getForm(PlayKitSelectionForm::FORM_UI_ID));
				}
			} else {
				$player->sendMessage(TF::RED . "You're already in a duel!");
			}
		}
	}

}