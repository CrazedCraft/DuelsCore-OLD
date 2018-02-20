<?php

declare(strict_types=1);

namespace duels\ui\elements\request;

use duels\DuelsPlayer;
use duels\ui\elements\generic\DuelTypeSelectionButton;
use duels\ui\windows\request\DuelRequestKitSelectionForm;
use pocketmine\utils\TextFormat as TF;

class DuelRequestDuelTypeSelectionButton extends DuelTypeSelectionButton {

	/**
	 * @param             $value
	 * @param DuelsPlayer $player
	 */
	public function handle($value, $player) {
		if(!$player->hasDuel()) {
			if($player->hasParty()) {
				$player->sendMessage(TF::RED . "You can't send a duel request whilst in a party!");
			} else {
				$player->setLastSelectedDuelType($this->getDuelType());
				$player->showModal($player->getCore()->getUiManager()->getForm(DuelRequestKitSelectionForm::FORM_UI_ID));
			}
		} else {
			$player->sendMessage(TF::RED . "You're already in a duel!");
		}
	}

}