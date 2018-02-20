<?php

/**
 * Duels_v1-Alpha – PartyEventSelector.php
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
 * Created on 25/7/17 at 8:30 PM
 *
 */

namespace duels\gui\item\party;

use core\CorePlayer;
use core\gui\item\GUIItem;
use core\language\LanguageUtils;
use core\Utils;
use duels\DuelsPlayer;
use duels\gui\containers\PartyEventSelectionContainer;
use pocketmine\item\Item;

class PartyEventSelector extends GUIItem {

	const GUI_ITEM_ID = "party_event_gui_item";

	public function __construct($parent = null) {
		parent::__construct(Item::get(Item::BOOK, 0, 1), $parent);
		$this->setCustomName(LanguageUtils::translateColors("&l&eStart party event"));
		$this->setPreviewName($this->getName());
	}

	/**
	 * @param DuelsPlayer|CorePlayer $player
	 *
	 * @return bool|void
	 */
	public function onClick(CorePlayer $player) {
		if(!$player->hasDuel()) {
			if($player->hasParty()) {
				if($player->getParty()->isOwner($player)) {
					$player->openGuiContainer($player->getCore()->getGuiManager()->getContainer(PartyEventSelectionContainer::CONTAINER_ID));
				} else {
					$player->sendMessage(Utils::translateColors("&cYou must be the party leader to start an event!"));
					return;
				}
			} else {
				$player->sendMessage(Utils::translateColors("&cYou must be in a party to start an event!"));
				return;
			}
		} else {
			$player->sendMessage(Utils::translateColors("&cYou're already in a duel!"));
			return;
		}
	}

	public function getCooldown() : int {
		return 3; // in seconds
	}

}