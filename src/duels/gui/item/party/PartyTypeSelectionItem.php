<?php

/**
 * Duels_v1-Alpha – PartyTypeSelectionItem.php
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
 * Created on 28/7/17 at 10:27 PM
 *
 */

namespace duels\gui\item\party;

use core\gui\item\GUIItem;
use core\Utils;
use duels\duel\Duel;
use duels\gui\containers\PartyEventSelectionContainer;
use pocketmine\item\Item;

class PartyTypeSelectionItem extends GUIItem {

	const GUI_ITEM_ID = "party_type_gui_selector";

	/** @var string */
	private $duelType = "";

	public function __construct(Item $item, string $type = Duel::TYPE_FFA, PartyEventSelectionContainer $parent = null, string $name = "") {
		$this->duelType = $type;
		parent::__construct($item, $parent);
		if($name !== "") {
			$this->setCustomName(Utils::translateColors($name));
			$this->setPreviewName($this->getName());
		}
	}

	public function getDuelType() : string {
		return $this->duelType;
	}

	public function setDuelType(string $value) {
		$this->duelType = $value;
	}

}