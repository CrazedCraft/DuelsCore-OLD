<?php

/**
 * SelectionNPC.php – DuelsCore
 *
 * Copyright (C) 2018 Jack Noordhuis
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Jack
 *
 */

declare(strict_types=1);

namespace duels\entity;

use core\entity\npc\HumanNPC;
use core\language\LanguageUtils;
use core\Utils;
use duels\arena\Arena;
use duels\duel\Duel;
use duels\duel\DuelType;
use duels\DuelsPlayer;
use duels\Main;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class SelectionNPC extends HumanNPC {

	/** @var DuelType */
	private $type = "";

	private $customName = "Duels";

	/**
	 * @param DuelType $type
	 */
	public function setType(DuelType $type) {
		$this->type = $type;
	}

	/**
	 * @return DuelType
	 */
	public function getType() : DuelType{
		return $this->type;
	}

	public function showPlaying($count) {
		$this->setNameTag(TF::YELLOW . LanguageUtils::centerPrecise($this->customName . "\n\n\n\n\n\n\n\n\n\n\n\n" . TF::BOLD . TF::YELLOW . $count . " playing" . TF::RESET, null));
	}

	public function initEntity() : void {
		parent::initEntity();
		if($this->namedtag->hasTag("customName") and ($n = $this->namedtag->getString("CustomName")) instanceof StringTag) {
			$this->customName = $n;
		}
	}

	public function attack(EntityDamageEvent $source): void {
		if($source instanceof EntityDamageByEntityEvent) {
			$attacker = $source->getDamager();
			if($attacker instanceof DuelsPlayer) {
				$plugin = Main::getInstance();
				if(!$attacker->hasDuel()) {
					if($attacker->hasParty()) {
						$party = $attacker->getParty();
						if($party->isOwner($attacker)) {
							if($this->getType()->getId() === DuelType::DUEL_TYPE_1V1) {
								if(count($party->getPlayers()) === 2) {
									$players = [];
									foreach($party->getPlayers() as $name => $uid) {
										$attacker = Utils::getPlayerByUUID($uid);
										if($attacker instanceof Player and $attacker->isOnline()) {
											$players[] = $attacker;
											if(count($players) === 2) {
												break;
											}
										} else {
											$attacker->sendMessage(TF::RED . "Cannot join duel due to {$name} being offline!");
											return;
										}
									}
									$arena = $plugin->getArenaManager()->find();
									if((!$arena instanceof Arena) or isset($this->plugin->duelManager->duels[$arena->getId()])) {
										$attacker->sendMessage(TF::RED . "Cannot find an open arena!");
										return;
									}
									$plugin->arenaManager->remove($arena->getId());
									$duel = new Duel($plugin, $this->type, $arena, $attacker->getLastSelectedKit());
									$attacker->removeLastSelectedKit();
									foreach($players as $p) {
										$duel->addPlayer($p);
									}
									$plugin->duelManager->duels[$arena->getId()] = $duel;
								} else {
									$attacker->sendPopup(TF::GOLD . "You can only play 1v1's in a party that has two players!");
								}
							} elseif($this->getType()->getId() === DuelType::DUEL_TYPE_2v2) {
								if(count($party->getPlayers()) === 4) {
									$players = [];
									foreach($party->getPlayers() as $name => $uid) {
										$attacker = Utils::getPlayerByUUID($uid);
										if($attacker instanceof Player and $attacker->isOnline()) {
											$players[] = $attacker;
											if(count($players) === 4) {
												break;
											}
										} else {
											$attacker->sendMessage(TF::RED . "Cannot join duel due to {$name} being offline!");
											return;
										}
									}
									$arena = $plugin->getArenaManager()->find();
									if(!($arena instanceof Arena) or isset($this->plugin->duelManager->duels[$arena->getId()])) {
										$attacker->sendMessage(TF::RED . "Cannot find an open arena!");
										return;
									}
									$plugin->arenaManager->remove($arena->getId());
									$duel = new Duel($plugin, $this->type, $arena, $attacker->getLastSelectedKit());
									$attacker->removeLastSelectedKit();
									foreach($players as $p) {
										$duel->addPlayer($p);
									}
									$plugin->duelManager->duels[$arena->getId()] = $duel;
								} else {
									$attacker->sendPopup(TF::GOLD . "You can only play 2v2's in a party that has four players!!");
								}
							} else {
								$attacker->sendPopup(TF::GOLD . "You've managed to break something!");
							}
						} else {
							$attacker->sendMessage(TF::RED . "Only the party leader can join a duel!");
						}
					} else {
						$plugin->duelManager->findDuel($attacker, $this->type->getId(), null, true);
					}
				} else {
					$attacker->sendPopup(TF::RED . "You're already in a duel!");
				}
			}
		}

		parent::attack($source);
	}

}