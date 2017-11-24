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
use core\CorePlayer;
use duels\DuelsPlayer;
use duels\Main;

class ToggleDuelCommand extends CoreUserCommand {

	public function __construct(Main $plugin) {
		parent::__construct($plugin->getCore(), "toggleduels", "Toggle's your ability to receive duel requests", "/toggleduels", ["toggleduel", "disableduels", "requests"]);
		$this->setPermission("duels.command.togglerequests");
	}

	/**
	 * @param CorePlayer|DuelsPlayer $player
	 * @param array      $args
	 *
	 * @return bool
	 */
	public function onRun(CorePlayer $player, array $args) {
		$player->setRequestsEnabled($current = !$player->hasRequestsEnabled());
		$player->sendTranslatedMessage(($current ? "ENABLED" : "DISABLED") . "_DUEL_REQUESTS");
		return true;
	}

}