<?php

/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 20/4/17
 * Time: 6:36 PM
 */

namespace duels\gui\containers;

use core\CorePlayer;
use core\gui\container\ChestGUI;
use core\gui\item\GUIItem;
use duels\gui\item\serverselectors\ClassicPvPServerSelector;
use duels\gui\item\serverselectors\DuelsServerSelector;
use pocketmine\inventory\BaseInventory;
use pocketmine\network\protocol\ContainerClosePacket;
use pocketmine\Player;

class ServerSelectionContainer extends ChestGUI {

	/** @var GUIItem[] */
	protected $defaultContents = [];

	public function __construct(CorePlayer $owner) {
		parent::__construct($owner);
		$this->defaultContents[] = new DuelsServerSelector($this);
		$this->defaultContents[] = new ClassicPvPServerSelector($this);
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
		$item->onClick($player);
		$player->removeWindow($this);
		return false; // don't remove the item
	}

}