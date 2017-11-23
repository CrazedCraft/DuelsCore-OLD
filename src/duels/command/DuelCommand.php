<?php

/**
 * DuelCommand.php class
 *
 * Created on 31/03/2016 at 10:10 PM
 *
 * @author Jack
 */


namespace duels\command\commands;

use core\command\CoreUserCommand;
use core\language\LanguageManager;
use duels\DuelsPlayer;

use duels\Main;

class DuelCommand extends CoreUserCommand {

	public function __construct(Main $plugin) {
		parent::__construct($plugin, "duel", "Allow's you to duel another player", "/duel <name>", "fight", "battle", "1v1");
		$this->setPermission("duels.command.duel");
	}

	public function onRun(DuelsPlayer $player, array $args) {
		if(isset($args[0])) {
			$target = $this->getPlugin()->getServer()->getPlayer($args[0]);
			if($target instanceof DuelsPlayer) {
				if(!$player->inDuel()) {
					if(!$target->inDuel()) {
						if($target->hasRequestFrom($player)) {
							// initiate duel
						} else {
							$target->addRequest($player);
							LanguageManager::translateForPlayer($player, "NEW_DUEL_REQUEST", [$player->getName()]);
							$target->sendMessage($this->getPlugin()->getLangugaeManger()->translateForPlayer($player, "NEW_DUEL_REQUEST", [$player->getName()]));
							$player->sendMessage($this->getPlugin()->getLangugaeManger()->translateForPlayer($player, "DUEL_REQUEST_SENT", [$target->getName()]));
						}
					} else {
						$player->sendMessage($this->getPlugin()->getLangugaeManger()->translateForPlayer($player, "USER_ALREADY_IN_DUEL", [$target->getName()]));
					}
				} else {
					$player->sendMessage($this->getPlugin()->getLangugaeManger()->translateForPlayer($player, "CANT_SEND_REQUEST_WHILE_IN_DUEL"));
				}
			} else {
				$player->sendMessage($this->getPlugin()->getLangugaeManger()->translateForPlayer($player, "USER_NOT_ONLINE", [$args[0]]));
			}
		} else {
			$player->sendMessage($this->getPlugin()->getLangugaeManger()->translateForPlayer($player, "PARTY_USAGE_MESSAGE"));
		}
	}

}