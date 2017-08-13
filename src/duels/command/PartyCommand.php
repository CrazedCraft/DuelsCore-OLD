<?php

/**
 * Duels_v1-Alpha – PartyCommand.php
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
 * Created on 24/7/17 at 6:03 PM
 *
 */

namespace duels\command;

use core\Utils;
use duels\Main;
use duels\party\Party;
use duels\session\PlayerSession;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class PartyCommand implements CommandExecutor {

	private $plugin;

	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
	}

	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {
		if($sender instanceof Player) {
			if(isset($args[0])) {
				switch(strtolower($args[0])) {
					case "invite":
					case "add":
						/** @var $requester Player */
						if(($requester = $this->plugin->getServer()->getPlayer($args[1])) instanceof Player) {
							if($sender->getName() === $requester->getName()) {
								$sender->sendMessage(TF::RED . "You cannot invite yourself to a party!");
								return true;
							}
							/** @var $sSession PlayerSession */
							if(!($sSession = $this->plugin->sessionManager->get($sender->getName())) instanceof PlayerSession) {
								$sender->kick(TF::RED . "Invalid session, rejoin to enjoy duels!");
								return true;
							}
							if($sSession->inParty()) {
								if($sSession->getParty()->isOwner($sender)) {
									if($sSession->getParty()->hasInvitation($requester->getName())) {
										$sender->sendMessage(TF::RED . $requester->getName() . " has already been invited to your party!");
										return true;
									}
									/** @var $rSession PlayerSession */
									if(!($rSession = $this->plugin->sessionManager->get($requester->getName())) instanceof PlayerSession) {
										$requester->kick(TF::RED . "Invalid session, rejoin to enjoy duels!");
									}
									$sSession->getParty()->invitePlayer($requester);
									return true;
								} else {
									$sender->sendMessage(TF::RED . "Only the party leader can invite players!");
									return true;
								}
							} else {
								$party = new Party($this->plugin->getPartyManager());
								$this->plugin->getPartyManager()->addParty($party);
								$party->addPlayer($sender, false);
								$party->setOwner($sender);
								$party->invitePlayer($requester);
							}
						} else {
							$sender->sendMessage(TF::GOLD . $args[1] . TF::RED . " is not online!");
							return true;
						}
						break;
					case "accept":
					case "join":
						if(isset($args[1])) {
							/** @var $requester Player */
							if(($requester = $this->plugin->getServer()->getPlayer($args[1])) instanceof Player) {
								if($sender->getName() === $requester->getName()) {
									$sender->sendMessage(TF::RED . "You cannot join your own party party!");
									return true;
								}
								/** @var $rSession PlayerSession */
								if(!($rSession = $this->plugin->sessionManager->get($requester->getName())) instanceof PlayerSession) {
									$requester->kick(TF::RED . "Invalid session, rejoin to enjoy duels!");
									return true;
								}
								if($rSession->inParty()) {
									if($rSession->getParty()->hasInvitation($sender->getName())) {
										$rSession->getParty()->acceptInvitation($sender);
										return true;
									} else {
										$sender->sendMessage(TF::RED . $requester->getName() . " has not invited you to their party!");
										return true;
									}
								} else {
									$sender->sendMessage(TF::RED . $requester->getName() . " does not have a party!");
									return true;
								}
							} else {
								$sender->sendMessage(TF::GOLD . $args[1] . TF::RED . " is not online!");
								return true;
							}
						} else {
							$sender->sendMessage(TF::RED . "You must specify a players party to join!");
							return true;
						}
						break;
					case "leave":
					case "quit":
						/** @var $session PlayerSession */
						if(!($session = $this->plugin->sessionManager->get($sender->getName())) instanceof PlayerSession) {
							$sender->kick(TF::RED . "Invalid session, rejoin to enjoy duels!");
							return true;
						}
						if($session->inParty()) {
							if($session->getParty()->isOwner($sender)) {
								$sender->sendMessage(TF::RED . "You cannot leave your own party! You must promote another player to leader or disband your party.");
								return true;
							}
							$session->getParty()->removePlayer($sender->getName());
						} else {
							$sender->sendMessage(TF::RED . "You must be in a party to use this command!");
							return true;
						}
						break;
					case "promote":
						/** @var $session PlayerSession */
						if(!($session = $this->plugin->sessionManager->get($sender->getName())) instanceof PlayerSession) {
							$sender->kick(TF::RED . "Invalid session, rejoin to enjoy duels!");
							return true;
						}
						if($session->inParty()) {
							if($session->getParty()->isOwner($sender)) {
								if(isset($args[1])) {
									/** @var $requester Player */
									if(($requester = $this->plugin->getServer()->getPlayer($args[1])) instanceof Player) {
										if($sender->getName() === $requester->getName()) {
											$sender->sendMessage(TF::RED . "You cannot promote yourself to leader!");
											return true;
										}
										/** @var $rSession PlayerSession */
										if(!($rSession = $this->plugin->sessionManager->get($requester->getName())) instanceof PlayerSession) {
											$requester->kick(TF::RED . "Invalid session, rejoin to enjoy duels!");
											return true;
										}
										if($rSession->inParty() and $rSession->getParty()->getId() === $session->getParty()->getId()) {
											$session->getParty()->setOwner($requester);
											$sender->sendMessage(TF::GOLD . "- " . TF::GREEN . $requester->getName() . " has been promoted to party leader!");
											$requester->sendMessage(TF::GOLD . "- ". TF::GREEN . $sender->getName() . " has promoted you to party leader!");
											return true;
										} else {
											$sender->sendMessage(TF::GOLD . $requester->getName() . TF::RED . " is not in your party!");
										}
									} else {
										$sender->sendMessage(TF::GOLD . $args[1] . TF::RED . " is not online!");
									}
								} else {
									$sender->sendMessage(TF::RED . "You must specify a player to promote to leader!");
									return true;
								}
							} else {
								$sender->sendMessage(TF::RED . "You must be the leader of a party to use this command!");
							return true;
							}
						} else {
							$sender->sendMessage(TF::RED . "You must be in a party to use this command!");
							return true;
						}
						break;
					case "kick":
					case "remove":
						/** @var $session PlayerSession */
						if(!($session = $this->plugin->sessionManager->get($sender->getName())) instanceof PlayerSession) {
							$sender->kick(TF::RED . "Invalid session, rejoin to enjoy duels!");
							return true;
						}
						if($session->inParty()) {
							if($session->getParty()->isOwner($sender)) {
								if(isset($args[1])) {
									/** @var $requester Player */
									if(($requester = $this->plugin->getServer()->getPlayer($args[1])) instanceof Player) {
										if($sender->getName() === $requester->getName()) {
											$sender->sendMessage(TF::RED . "You cannot kick yourself from your party!!");
											return true;
										}
										/** @var $rSession PlayerSession */
										if(!($rSession = $this->plugin->sessionManager->get($requester->getName())) instanceof PlayerSession) {
											$requester->kick(TF::RED . "Invalid session, rejoin to enjoy duels!");
											return true;
										}
										if($rSession->inParty() and $rSession->getParty()->getId() === $session->getParty()->getId()) {
											$session->getParty()->kickPlayer($requester->getName());
											$requester->sendMessage(TF::GOLD . "- ". TF::GREEN . $sender->getName() . " has removed you from their party");
											return true;
										} else {
											$sender->sendMessage(TF::GOLD . $requester->getName() . TF::RED . " is not in your party!");
											return true;
										}
									} else {
										if($session->getParty()->inParty($args[1])) {
											$session->getParty()->kickPlayer($args[1]);
											return true;
										} else {
											$sender->sendMessage(TF::GOLD . $args[1] . TF::RED . " is not in your party!");
											return true;
										}
									}
								} else {
									$sender->sendMessage(TF::RED . "You must specify a player to remove!");
									return true;
								}
							} else {
								$sender->sendMessage(TF::RED . "You must be the leader of a party to use this command!");
								return true;
							}
						} else {
							$sender->sendMessage(TF::RED . "You must be in a party to use this command!");
							return true;
						}
						break;
					case "disband":
						/** @var $session PlayerSession */
						if(!($session = $this->plugin->sessionManager->get($sender->getName())) instanceof PlayerSession) {
							$sender->kick(TF::RED . "Invalid session, rejoin to enjoy duels!");
							return true;
						}
						if($session->inParty()) {
							if($session->getParty()->isOwner($sender)) {
								$session->getParty()->disband("{$sender->getName()} disbanding the party");
							} else {
								$sender->sendMessage(TF::RED . "You must be the leader of a party to use this command!");
								return true;
							}
						} else {
							$sender->sendMessage(TF::RED . "You must be in a party to use this command!");
							return true;
						}
						break;
					case "list" :
						/** @var $session PlayerSession */
						if(!($session = $this->plugin->sessionManager->get($sender->getName())) instanceof PlayerSession) {
							$sender->kick(TF::RED . "Invalid session, rejoin to enjoy duels!");
							return true;
						}
						if($session->inParty()) {
							$session->getParty()->sendList($sender);
						} else {
							$sender->sendMessage(TF::RED . "You must be in a party to use this command!");
							return true;
						}
						break;
					case "help":
						$sender->sendMessage(Utils::translateColors("&e==== &bParty Commands &e====\n&b/party invite <player> &7- &eInvite a player to your party\n&b/party accept <player> &7- &eAccept a party invitation\n&b/party leave &7- &eLeave your current party\n&b/party remove <player> &7- &eRemove a player from your party\n&b/party disband &7- &eDisband your party\n&b/party list &7- &eList all the members in your party\n&b/party help &7- &eDisplays this wonderful message c:"));
						break;
					default:
						$sender->sendMessage(TF::RED . "- " . TF::GOLD . "Unknown party command, do /party help for info on party commands.");
						break;
				}
			} else {
				$sender->sendMessage(TF::RED . "Usage: /party <invite|kick|promote|leave|disband>");
				return true;
			}
		} else {
			$sender->sendMessage(TF::RED . "Please run this command in-game!");
			return true;
		}
		return true;
	}

}