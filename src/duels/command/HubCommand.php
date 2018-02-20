<?php

/**
 * Old-Duels – HubCommand.php
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
 * Created on 7/7/17 at 8:41 AM
 *
 */

namespace duels\command;

use duels\DuelsPlayer;
use duels\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class HubCommand implements CommandExecutor {

	private $plugin;

	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
	}

	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {
		if($sender instanceof DuelsPlayer) {
			if($sender->hasDuel()) {
				$duel = $sender->getDuel();
				$duel->broadcast(TextFormat::LIGHT_PURPLE . $sender->getName() . TextFormat::GOLD . " left the duel!");
				$duel->handleDeath($sender);
			}
			$sender->teleport(Main::$spawnCoords);
			$sender->sendMessage(TextFormat::GOLD . "- " . TextFormat::GREEN . "You have been teleported to spawn!");
		} else {
			$sender->sendMessage(TextFormat::RED . "Please run this command in-game!");
			return true;
		}
		return true;
	}

}