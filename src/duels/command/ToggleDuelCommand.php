<?php

/**
 * ToggleDuelCommand class
 *
 * Created on 31/03/2016 at 10:17 PM
 *
 * @author Jack
 */


namespace duels\command\commands;

use core\command\CoreUserCommand;
use core\language\LanguageManager;
use duels\DuelsPlayer;
use duels\Main;

class ToggleDuelCommand extends CoreUserCommand {

	public function __construct(Main $plugin) {
		parent::__construct($plugin->getCore(), "toggleduels", "Toggle's your ability to receive duel requests", "/toggleduels", ["toggleduel", "disableduels"]);
		$this->setPermission("duels.command.toggleduels");
	}

	public function onRun(DuelsPlayer $player, array $args) {
		if($player->hasRequestsEnabled()) {
			$player->setRequests(false);
			LanguageManager::translateForPlayer($player, "DISABLED_DUEL_REQUESTS");
		} else {
			$player->setRequests(true);
			LanguageManager::translateForPlayer($player, "ENABLED_DUEL_REQUESTS");
		}
	}

}