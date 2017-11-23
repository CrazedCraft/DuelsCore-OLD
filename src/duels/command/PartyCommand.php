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
use duels\command\DuelsUserCommand;
use duels\DuelsPlayer;

use duels\Main;

class PartyCommand extends CoreUserCommand {

	public function __construct(Main $plugin) {
		parent::__construct($plugin, "party", "Allow's you to create and join a party", "/party <name>", ["p", "team"]);
		$this->setPermission("duels.command.party");
	}

	public function onRun(DuelsPlayer $player, array $args) {
		if(isset($args[0])) {
			$target = $this->getPlugin()->getServer()->getPlayer($args[0]);
			if($target instanceof DuelsPlayer) {
				if(!$player->inParty()) {
					$this->getPlugin()->getPartyManager()->openParty($player);
				}
				if(!$target->inParty()) {
					if(!$player->getParty()->hasRequest($target)) {
						$player->getParty()->addRequest($target);
						$target->sendMessage($this->getPlugin()->getLangugaeManger()->translateForPlayer($player, "NEW_PARTY_REQUEST", [$player->getName()]));
						$player->sendMessage($this->getPlugin()->getLangugaeManger()->translateForPlayer($player, "PARTY_SENT_REQUEST", [$target->getName()]));
					} else {
						$player->sendMessage($this->getPlugin()->getLangugaeManger()->translateForPlayer($player, "PARTY_ALREADY_HAS_REQUEST", [$target->getName()]));
					}
				} else {
					$player->sendMessage($this->getPlugin()->getLangugaeManger()->translateForPlayer($player, "USER_ALREADY_IN_PARTY", [$target->getName()]));
				}
			} else {
				$player->sendMessage($this->getPlugin()->getLangugaeManger()->translateForPlayer($player, "USER_NOT_ONLINE", [$args[0]]));
			}
		} else {
			$player->sendMessage($this->getPlugin()->getLangugaeManger()->translateForPlayer($player, "PARTY_USAGE_MESSAGE"));
		}
		return true;
	}

}