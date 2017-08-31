<?php

/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 20/4/17
 * Time: 6:51 PM
 */

namespace duels\gui\containers;

use core\CorePlayer;
use core\gui\container\ChestGUI;
use core\gui\item\GUIItem;
use duels\duel\Duel;
use duels\gui\item\kit\KitGUIItem;
use duels\kit\RandomKit;
use duels\Main;
use duels\session\PlayerSession;
use pocketmine\inventory\BaseInventory;
use pocketmine\network\protocol\ContainerClosePacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class KitSelectionContainer extends ChestGUI {

	/** @var GUIItem[] */
	protected $defaultContents = [];

	public function __construct(CorePlayer $owner) {
		parent::__construct($owner);
		foreach(Main::getInstance()->getKitManager()->getSelectionItems() as $item) {
			$this->defaultContents[] = new KitGUIItem($item, $this);
		}
		$this->setContents($this->defaultContents);
	}

	public function onOpen(Player $who) {
		$this->setContents($this->defaultContents);
		parent::onOpen($who);
	}

	public function close(Player $who) {
		$pk = new ContainerClosePacket();
		$pk->windowid = $who->getWindowId($this);
		$who->directDataPacket($pk);
		BaseInventory::onClose($who);
	}

	public function onSelect($slot, GUIItem $item, CorePlayer $player) {
		$player->removeWindow($this);
		if(!$item instanceof KitGUIItem) {
			throw new \InvalidArgumentException("Expected duels/gui/item/kit/KitGUIItem, got core/gui/item/GUIItem instead");
		}
		$plugin = Main::getInstance();
		/** @var $session PlayerSession */
		if(!($session = $plugin->sessionManager->get($player->getName())) instanceof PlayerSession) {
			$player->kick(TF::RED . "Invalid session, rejoin to enjoy duels!");
			return true;
		}
		if(!$session->inDuel()) {
			if($session->inParty() and !$session->getParty()->isOwner($player)) {
				$player->sendMessage(TF::RED . "Only the party leader can join a duel!");
				return true;
			}
			Main::getInstance()->getDuelManager()->findDuel($player, Duel::TYPE_1V1, $item->getKit() instanceof RandomKit ? null : $item->getKit(), true);
		} else {
			$player->sendMessage(TF::RED . "You're already in a duel!");
			return true;
		}
		return false; // don't remove the item
	}

}