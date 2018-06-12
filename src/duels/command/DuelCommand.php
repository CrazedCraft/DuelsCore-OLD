<?php

namespace duels\command;

use duels\arena\Arena;
use duels\duel\Duel;
use duels\duel\DuelType;
use duels\DuelsPlayer;
use duels\kit\Kit;
use duels\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;

class DuelCommand implements CommandExecutor {

	private $plugin;

	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
		if($sender instanceof DuelsPlayer) {
			if(isset($args[0])) {
				$requester = $this->plugin->getServer()->getPlayer($args[0]);
				if($requester instanceof DuelsPlayer) {
					if($sender->getName() === $requester->getName()) {
						$sender->sendMessage(TF::RED . "You cannot duel yourself!");
						return true;
					}
					if($sender->hasDuelRequest($requester->getName())) {
						if(!$sender->hasDuel()) {
							if(!$requester->hasDuel()) {
								$sender->removeRequest($requester->getName());
								$arena = $this->plugin->getArenaManager()->find();
								if((!$arena instanceof Arena) or isset($this->plugin->duelManager->duels[$arena->getId()])) {
									$sender->sendMessage(TF::RED . "Cannot find an open arena!");
									return true;
								}
								$this->plugin->arenaManager->remove($arena->getId());
								$requester->sendMessage(TF::GOLD . TF::BOLD . $sender->getName() . TF::RESET . TF::GREEN . " has accepted your Duel request!");
								$sender->sendMessage(TF::GREEN . "You have accepted a Duel request from " . TF::GOLD . TF::BOLD . $requester->getName() . TF::RESET . TF::GREEN . "!");
								$kit = $requester->getLastSelectedKit();
								$duel = new Duel($this->plugin, $this->plugin->duelManager->getDuelType(DuelType::DUEL_TYPE_1V1), $arena, ($kit instanceof Kit and $kit->getType() === Kit::TYPE_KIT) ? $kit : $this->plugin->getKitManager()->getRandomKit());
								$requester->removeLastSelectedKit();
								$requester->removeLastTappedPlayer();
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
						if(!$requester->hasDuelRequest($sender->getName())) {
							$requester->addRequest($sender, $requester);
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