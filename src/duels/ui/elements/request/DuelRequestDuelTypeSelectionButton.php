<?php

declare(strict_types=1);

namespace duels\ui\elements\request;

use duels\DuelsPlayer;
use duels\Main;
use duels\session\PlayerSession;
use duels\ui\elements\generic\DuelTypeSelectionButton;
use duels\ui\windows\request\DuelRequestKitSelectionForm;
use pocketmine\utils\TextFormat as TF;

class DuelRequestDuelTypeSelectionButton extends DuelTypeSelectionButton {

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
				if($session->inParty()) {
					$player->sendMessage(TF::RED . "You can't send a duel request whilst in a party!");
				} else {
					$session->lastSelectedPlayType = $this->getDuelTypeId();
					$player->showModal($plugin->getCore()->getUiManager()->getForm(DuelRequestKitSelectionForm::FORM_UI_ID));
				}
			} else {
				$player->sendMessage(TF::RED . "You're already in a duel!");
			}
		}
	}
}