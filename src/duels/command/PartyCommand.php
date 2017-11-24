<?php

/**
 * PartyCommand.php class
 *
 * Created on 31/03/2016 at 10:10 PM
 *
 * @author Jack
 */


namespace duels\command\commands;

use core\command\CoreUserCommand;
use core\CorePlayer;
use duels\command\DuelsUserCommand;
use duels\DuelsPlayer;

use duels\Main;

class PartyCommand extends CoreUserCommand {

	public function __construct(Main $plugin) {
		parent::__construct($plugin->getCore(), "party", "Allow's you to create and join a party", "/party <name>", ["p", "team"]);
		$this->setPermission("duels.command.party");
	}

	/**
	 * @param CorePlayer|DuelsPlayer $player
	 * @param array       $args
	 *
	 * @return bool
	 */
	public function onRun(CorePlayer $player, array $args) {
		return true;
	}

}