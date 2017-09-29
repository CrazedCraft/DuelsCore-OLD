<?php

namespace duels\command;

use duels\arena\Arena;
use duels\duel\Duel;
use duels\kit\Kit;
use duels\kit\RandomKit;
use duels\Main;
use duels\session\PlayerSession;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class DuelCommand implements CommandExecutor {

	private $plugin;

	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
	}

	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {
		if($sender instanceof Player) {
			if(isset($args[0])) {
				if(($requester = $this->plugin->getServer()->getPlayer($args[0])) instanceof Player) {
					if(!($sSession = $this->plugin->sessionManager->get($sender->getName())) instanceof PlayerSession) {
						$sender->kick(TF::RED . "Invalid session, rejoin to enjoy duels!", false);
						return true;
					}
					if(!($rSession = $this->plugin->sessionManager->get($requester->getName())) instanceof PlayerSession) {
						$requester->kick(TF::RED . "Invalid session, rejoin to enjoy duels!", false);
					}
					if($sender->getName() === $requester->getName()) {
						$sender->sendMessage(TF::RED . "You cannot duel yourself!");
						return;
					}
					if($sSession->hasRequest($requester->getName())) {
						if(!$sSession->inDuel()) {
							if(!$rSession->inDuel()) {
								$sSession->removeRequest($requester->getName());
								$arena = $this->plugin->getArenaManager()->find();
								if((!$arena instanceof Arena) or isset($this->plugin->duelManager->duels[$arena->getId()])) {
									$sender->sendMessage(TF::RED . "Cannot find an open arena!");
									return true;
								}
								$this->plugin->arenaManager->remove($arena->getId());
								$requester->sendMessage(TF::GOLD . TF::BOLD . $sender->getName() . TF::RESET . TF::GREEN . " has accepted your Duel request!");
								$sender->sendMessage(TF::GREEN . "You have accepted a Duel request from " . TF::GOLD . TF::BOLD . $requester->getName() . TF::RESET . TF::GREEN . "!");
								$duel = new Duel($this->plugin, Duel::TYPE_1V1, $arena, ($rSession->lastSelectedKit instanceof Kit and !($rSession->lastSelectedKit instanceof RandomKit)) ? $rSession->lastSelectedKit : $this->plugin->getKitManager()->findRandom());
								$rSession->lastSelectedKit = null;
								$duel->addPlayer($sender);
								$duel->addPlayer($requester);
								$this->plugin->duelManager->duels[$arena->getId()] = $duel;
								return true;
							} else {
								$sender->sendMessage(TF::GOLD . $requester->getName() . TF::RED . " is currently in a duel, try again in a moment!");
								return true;
							}
						} else {
							$sender->sendMessage(TF::RED . "You cannot accept duel request while in a duel!");
							return true;
						}
					} else {
						if(!$rSession->hasRequest($sender->getName())) {
							$rSession->addRequest($sender, $requester);
							$sender->sendMessage(TF::AQUA . "Sent a Duel request to " . TF::BOLD . TF::GREEN . $requester->getName() . TF::RESET . TF::AQUA . "!");
							return true;
						} else {
							$sender->sendMessage(TF::RED . "You've already sent " . TF::GOLD . $requester->getName() . TF::RED . " a request!");
							return true;
						}
					}
				} else {
					$sender->sendMessage(TF::RED . $args[0] . " isn't online!");
					return true;
				}
			} else {
				$sender->sendMessage(TF::RED . "Please specify a player!");
				return true;
			}
		} else {
			$sender->sendMessage(TF::RED . "Please run this command in-game!");
			return true;
		}
	}

}
