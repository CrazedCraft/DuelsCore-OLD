<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 20/4/17
 * Time: 9:58 PM
 */

namespace duels\gui\item\serverselectors;

use core\CorePlayer;
use core\gui\item\GUIItem;
use core\language\LanguageUtils;
use duels\Main;
use duels\ui\windows\DefaultServerSelectionForm;
use pocketmine\item\Item;
use pocketmine\network\protocol\Info;

class ServerSelector extends GUIItem {

	public function __construct($parent = null) {
		parent::__construct(Item::get(Item::COMPASS, 0, 1), $parent);
		$this->setCustomName(LanguageUtils::translateColors("&l&dServer Selector"));
		$this->setPreviewName($this->getName());
	}

	public function onClick(CorePlayer $player) {
		if($player->getPlayerProtocol() >= Info::PROTOCOL_120) {
			$player->showModal(Main::getInstance()->getUIManager()->getForm(DefaultServerSelectionForm::FORM_UI_ID));
		} else {
			$player->addWindow($player->getGuiContainer(Main::GUI_SERVER_SELECTION_CONTAINER));
		}
	}

	public function getCooldown() : int {
		return 5; // in seconds
	}

}