<?php

declare(strict_types=1);

namespace duels\ui\elements\play;

use duels\DuelsPlayer;
use duels\ui\elements\generic\DuelTypeSelectionButton;
use duels\ui\windows\play\PlayKitSelectionForm;
use pocketmine\utils\TextFormat as TF;

class PlayDuelTypeSelectionButton extends DuelTypeSelectionButton {

	/**
	 * @param             $value
	 * @param DuelsPlayer $player
	 */
	public function handle($value, $player) {
		if(!$player->hasDuel()) {
			if($player->hasParty() and !$player->getParty()->isOwner($player)) {
				$player->sendMessage(TF::RED . "Only the party leader can join a duel!");
			} else {
				$player->setLastSelectedDuelType($this->getDuelType());
				$player->showModal($player->getCore()->getUiManager()->getForm(PlayKitSelectionForm::FORM_UI_ID));
			}
		} else {
			$player->sendMessage(TF::RED . "You're already in a duel!");
		}
	}

}