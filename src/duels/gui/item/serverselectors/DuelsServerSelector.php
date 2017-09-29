<?php

/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 20/4/17
 * Time: 6:39 PM
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

class DuelsServerSelector extends GUIItem {

	const GUI_ITEM_ID = "duels_gui_selector";

	/** @var int */
	protected $nodeId = -1;

	public function __construct(ServerSelectionContainer $parent = null, $nodeId = 1) {
		$this->nodeId = $nodeId;
		parent::__construct(Item::get(Item::DIAMOND_SWORD, 0, $nodeId), $parent);
		$this->setCustomName(LanguageUtils::translateColors("&l&bDuel {$nodeId}"));
		$this->setPreviewName($this->getName());
		if(Main::getInstance()->getNetworkManager()->getServer()->getId() === $nodeId) {
			$this->giveEnchantmentEffect();
		}
	}

	public function onClick(CorePlayer $player) {
		$player->sendMessage(LanguageUtils::translateColors("&6- &aTransferring..."));
		$server = Main::getInstance()->getNetworkManager()->getNodes()[NodeConstants::NODE_DUEL]->getServers()[$this->nodeId];
		if($server instanceof NetworkServer) {
			$pk = new TransferPacket();
			$pk->ip = $server->getHost();
			$player->dataPacket($pk);
		} else {
			$player->sendMessage(LanguageUtils::translateColors("&c- &6Duel-{$this->nodeId} is currently offline!"));
		}
	}

}