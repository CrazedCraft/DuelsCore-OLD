<?php

/**
 * Duels_v1-Alpha – ClassicPrisonServerSelector.php
 *
 * Copyright (C) 2017 Jack Noordhuis
 *
 * This is private software, you cannot redistribute and/or modify it in any way
 * unless given explicit permission to do so. If you have not been given explicit
 * permission to view or modify this software you should take the appropriate actions
 * to remove this software from your device immediately.
 *
 * @author Jack Noordhuis
 *
 * Created on 31/8/17 at 5:16 PM
 *
 */

namespace duels\gui\item\serverselectors;

use core\CorePlayer;
use core\gui\item\GUIItem;
use core\language\LanguageUtils;
use core\Main;
use core\network\NetworkServer;
use core\network\NodeConstants;
use duels\gui\containers\ServerSelectionContainer;
use pocketmine\item\Item;
use pocketmine\network\protocol\TransferPacket;

class ClassicPrisonServerSelector extends GUIItem {

	const GUI_ITEM_ID = "classic_prison_gui_selector";

	public function __construct(ServerSelectionContainer $parent = null) {
		parent::__construct(Item::get(Item::IRON_PICKAXE, 0, 1), $parent);
		$this->setCustomName(LanguageUtils::translateColors("&l&aClassic Prison"));
		$this->setPreviewName($this->getName());
	}

	public function onClick(CorePlayer $player) {
		$player->sendMessage(LanguageUtils::translateColors("&6- &aTransferring..."));
		$server = Main::getInstance()->getNetworkManager()->getNodes()[NodeConstants::NODE_CLASSIC_PRISON]->getSuitableServer();
		if($server instanceof NetworkServer) {
			$pk = new TransferPacket();
			$pk->ip = $server->getHost();
			$player->dataPacket($pk);
		} else {
			$player->sendMessage(LanguageUtils::translateColors("&c- &6There are currently no classic prison servers available!"));
		}
	}

}