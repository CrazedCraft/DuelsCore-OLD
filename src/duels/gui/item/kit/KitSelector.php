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
use duels\Main;
use duels\session\PlayerSession;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat as TF;

class KitSelector extends GUIItem {

	public function __construct($parent = null) {
		parent::__construct(Item::get(Item::DIAMOND_SWORD, 0, 1), $parent);
		$this->setCustomName(LanguageUtils::translateColors("&l&6Play"));
	}

	public function onClick(CorePlayer $player) {
		$plugin = Main::getInstance();
		/** @var $session PlayerSession */
		if(!($session = $plugin->sessionManager->get($player->getName())) instanceof PlayerSession) {
			$player->kick(TF::RED . "Invalid session, rejoin to enjoy duels!");
			return;
		}
		if(!$session->inParty()) {
			$player->addWindow($player->getGuiContainer(Main::GUI_KIT_SELECTION_CONTAINER));
		} else {
			$player->sendMessage(TF::RED . "You must use the party event item or the NPC's to play duels while in a party!");
		}
	}

	public function getCooldown() : int {
		return 1; // in seconds
	}

}