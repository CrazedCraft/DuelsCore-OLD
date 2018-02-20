<?php

/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 20/4/17
 * Time: 9:56 PM
 */

namespace duels\gui\item\kit;

use core\CorePlayer;
use core\gui\item\GUIItem;
use core\language\LanguageUtils;
use duels\DuelsPlayer;
use duels\gui\containers\play\PlayDuelTypeSelectionContainer;
use duels\ui\windows\play\PlayDuelTypeSelectionForm;
use pocketmine\item\Item;
use pocketmine\network\protocol\Info;
use pocketmine\utils\TextFormat as TF;

class KitSelector extends GUIItem {

	const GUI_ITEM_ID = "play_gui_item";

	public function __construct($parent = null) {
		parent::__construct(Item::get(Item::DIAMOND_SWORD, 0, 1), $parent);
		$this->setCustomName(LanguageUtils::translateColors("&l&6Play"));
		$this->setPreviewName($this->getName());
	}

	/**
	 * @param DuelsPlayer|CorePlayer $player
	 *
	 * @return bool|void
	 */
	public function onClick(CorePlayer $player) {
		if(!$player->hasParty()) {
			if($player->getPlayerProtocol() >= Info::PROTOCOL_120) {
				$player->showModal($player->getCore()->getUIManager()->getForm(PlayDuelTypeSelectionForm::FORM_UI_ID));
			} else {
				$player->openGuiContainer($player->getCore()->getGuiManager()->getContainer(PlayDuelTypeSelectionContainer::CONTAINER_ID));
			}
		} else {
			$player->sendMessage(TF::RED . "You must use the party event item or the NPC's to play duels while in a party!");
		}
	}

	public function getCooldown() : int {
		return 3; // in seconds
	}

}