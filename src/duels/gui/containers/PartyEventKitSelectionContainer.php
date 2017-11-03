<?php

/**
 * Duels_v1-Alpha – PartyEventKitSelectionContainer.phpontainer.php
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
 * Created on 24/7/17 at 10:43 PM
 *
 */

namespace duels\gui\containers;

use core\CorePlayer;
use core\gui\container\ChestGUI;
use core\gui\item\GUIItem;
use core\Utils;
use duels\arena\Arena;
use duels\duel\Duel;
use duels\gui\item\kit\KitGUIItem;
use duels\kit\RandomKit;
use duels\Main;
use duels\session\PlayerSession;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class PartyEventKitSelectionContainer extends ChestGUI {

	const CONTAINER_ID = "party_kit_selection_container";

	/** @var GUIItem[] */
	protected $defaultContents = [];

	public function __construct(Main $plugin) {
		parent::__construct($plugin->getCore());

		foreach($plugin->getKitManager()->getSelectionItems() as $item) {
			$this->defaultContents[] = new KitGUIItem($item);
		}
		$this->setContents($this->defaultContents);
	}

	public function onOpen(Player $who) {
		$this->setContents($this->defaultContents);
		parent::onOpen($who);
	}

	public function onSelect(int $slot, GUIItem $item, CorePlayer $player) : bool {
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
		if($session->inParty()) {
			if($session->lastSelectedPartyType !== "") {
				$players = [];
					foreach($session->getParty()->getPlayers() as $name => $uid) {
						$player = Utils::getPlayerByUUID($uid);
						if($player instanceof Player and $player->isOnline()) {
							$players[] = $player;
						} else {
							$player->sendMessage(TF::RED . "Cannot start party event due to {$name} being offline!");
							return false;
						}
					}
				$arena = $plugin->getArenaManager()->find();
				if((!$arena instanceof Arena) or isset($plugin->duelManager->duels[$arena->getId()])) {
					$player->sendMessage(TF::RED . "Cannot find an open arena!");
					return false;
				}
				$plugin->arenaManager->remove($arena->getId());
				$duel = new Duel($plugin, $session->lastSelectedPartyType, $arena, $item->getKit() instanceof RandomKit ? $plugin->getKitManager()->findRandom() : $item->getKit());
				$session->lastSelectedPartyType = "";
				foreach($players as $p) {
					if($duel->isJoinable() or $duel->getType() === Duel::TYPE_FFA) {
						$duel->addPlayer($p);
					} else {
						break;
					}
				}
				$plugin->duelManager->duels[$arena->getId()] = $duel;
			} else {
				$player->sendMessage(Utils::translateColors("&cYou must select a duel type to start an event!"));
				return false;
			}
		} else {
			$player->sendMessage(Utils::translateColors("&cYou must be in a party to start an event!"));
			return false;
		}

		return false; // don't remove the item
	}

}