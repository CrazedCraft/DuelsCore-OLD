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
use duels\arena\Arena;
use duels\duel\Duel;
use duels\Main;
use duels\session\PlayerSession;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class PartyEventSelector extends GUIItem {

	public function __construct($parent = null) {
		parent::__construct(Item::get(Item::BOOK, 0, 1), $parent);
		$this->setCustomName(LanguageUtils::translateColors("&l&eStart party event"));
	}

	public function onClick(CorePlayer $player) {
		$plugin = Main::getInstance();
		/** @var $session PlayerSession */
		if(!($session = $plugin->sessionManager->get($player->getName())) instanceof PlayerSession) {
			$player->kick(TF::RED . "Invalid session, rejoin to enjoy duels!");
			return;
		}
		if(!$session->inDuel()) {
			if($session->inParty()) {
				if($session->getParty()->isOwner($player)) {
					$player->addWindow($player->getGuiContainer(Main::GUI_PARTY_TYPE_SELECTION_CONTAINER));
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

	public function getPreviewDescription(CorePlayer $player) : string {
		return LanguageUtils::translateColors("&o&7Tap the item on the ground to use.");
	}

	public function getCooldown() : int {
		return 5; // in seconds
	}

}