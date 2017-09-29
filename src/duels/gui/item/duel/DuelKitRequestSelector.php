<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 20/4/17
 * Time: 9:30 PM
 */

namespace duels\gui\item\duel;

use core\CorePlayer;
use core\gui\item\GUIItem;
use core\language\LanguageUtils;
use duels\Main;
use duels\ui\windows\DuelRequestKitSelectionForm;
use pocketmine\item\Item;
use pocketmine\network\protocol\Info;

class DuelKitRequestSelector extends GUIItem {

	const GUI_ITEM_ID = "duel_request_selector";

	public function __construct($parent = null) {
		parent::__construct(Item::get(Item::STICK, 0, 1), $parent);
		$this->setCustomName(LanguageUtils::translateColors("&l&aDuel Stick"));
		$this->setPreviewName($this->getName());
		$this->setPreviewDescription(LanguageUtils::translateColors("&o&7Tap the item on a player to use."));
	}

	public function onClick(CorePlayer $player) {
		$plugin = Main::getInstance();
		if($player->getPlayerProtocol() >= Info::PROTOCOL_120) {
			$player->showModal($plugin->getUIManager()->getForm(DuelRequestKitSelectionForm::FORM_UI_ID));
		} else {
			$player->addWindow($player->getGuiContainer(Main::GUI_DUEL_SELECTION_CONTAINER));
		}
	}

	public function getCooldown() : int {
		return 5; // in seconds
	}

}