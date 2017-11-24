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
use core\CorePlayer;
use core\language\LanguageManager;
use duels\DuelsPlayer;

use duels\Main;

class DuelCommand extends CoreUserCommand {

	public function __construct(Main $plugin) {
		parent::__construct($plugin->getCore(), "duel", "Allow's you to duel another player", "/duel <name>", "fight", "battle", "1v1");
		$this->setPermission("duels.command.duel");
	}

	/**
	 * @param CorePlayer|DuelsPlayer $player
	 * @param array      $args
	 *
	 * @return bool
	 */
	public function onRun(CorePlayer $player, array $args) {
		return true;
	}

}