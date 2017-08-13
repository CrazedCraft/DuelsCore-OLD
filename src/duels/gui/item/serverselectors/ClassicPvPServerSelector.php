<?php

/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 20/4/17
 * Time: 6:45 PM
 */

namespace duels\gui\item\serverselectors;

use core\CorePlayer;
use core\gui\item\GUIItem;
use core\language\LanguageUtils;
use duels\gui\containers\ServerSelectionContainer;
use pocketmine\item\Item;
use pocketmine\network\protocol\TransferPacket;

class ClassicPvPServerSelector extends GUIItem {

	const GUI_ITEM_ID = "classic_pvp_server_selector";

	public function __construct(ServerSelectionContainer $parent = null) {
		parent::__construct(Item::get(Item::GOLD_CHESTPLATE, 0, 1), $parent);
		$this->setCustomName(LanguageUtils::translateColors("&l&6Classic PvP"));
	}

	public function getCooldown() : int {
		return 0; // in seconds
	}

	public function onClick(CorePlayer $player) {
		$player->sendMessage(LanguageUtils::translateColors("&6- &aTransferring..."));
		$pk = new TransferPacket();
		$pk->ip = "cpvp.crazedcraftmc.net";
		$player->dataPacket($pk);
	}

}