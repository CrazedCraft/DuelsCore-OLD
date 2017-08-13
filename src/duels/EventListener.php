<?php

namespace duels;

use core\CorePlayer;
use core\gui\item\GUIItem;
use core\Utils;
use duels\arena\Arena;
use duels\duel\Duel;
use duels\entity\HumanNPC;
use duels\gui\item\duel\DuelKitRequestSelector;
use duels\kit\Kit;
use duels\kit\RandomKit;
use duels\session\PlayerSession;
use pocketmine\block\Block;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\item\Item;
use pocketmine\network\protocol\InteractPacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class EventListener implements Listener {

	public $needAuth = [];
	public $attempts = [];
	public $lastMsg = [];
	private $plugin;

	/** @var Item */
	private $air;

	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
		$this->air = Item::get(Item::AIR);
	}

	public function onQuery(QueryRegenerateEvent $event) {
		$event->setServerName(Main::translateColors("&1C&ar&ea&6z&9e&5d&fC&7r&6a&cf&dt &l&6Duels&r"));
	}

	public function onLogin(PlayerLoginEvent $event) {
		$event->getPlayer()->setSpawn(Main::$spawnCoords);
		$event->getPlayer()->teleport(Main::$spawnCoords);
	}

	public function onJoin(PlayerJoinEvent $event) {
		$event->setJoinMessage("");
		$player = $event->getPlayer();
		$player->setFoodEnabled(false);
		$player->setFood(20);
		$session = $this->plugin->sessionManager->get($player->getName());
		if($session instanceof PlayerSession) {
			$session->close();
		}
		$this->plugin->getSessionManager()->add($player);
		$player->setNameTag(TF::YELLOW . $player->getName());
		$this->plugin->addGuiConatiners($player);
		$this->plugin->giveLobbyItems($player);
		//$this->plugin->lobbyBossBar->spawnTo($player);
	}

	//public function onMove(PlayerMoveEvent $event) {
	//	$player = $event->getPlayer();
	//	/** @var PlayerSession $session */
	//	$session = $this->plugin->sessionManager->get($player->getName());
	//	if($session instanceof PlayerSession) {
	//		if($session->getStatus() === PlayerSession::STATUS_WAITING) {
	//			foreach($player->level->getNearbyEntities($player->boundingBox->grow(4, 4, 4), $player) as $entity) {
	//				$distance = $player->distance($entity);
	//				if(!$entity instanceof HumanNPC)
	//					continue;
	//				if($distance <= 1.8)
	//					$player->knockBack($entity, 0, ($player->x - $entity->x), ($player->z - $entity->z), 1);
	//				if($distance <= 8) {
	//					$entity->look($player);
	//				} else {
	//					$entity->resetLook($player);
	//				}
	//			}
	//		}
	//	}
	//}

	public function onDamage(EntityDamageEvent $event) {
		$victim = $event->getEntity();
		if($victim instanceof Player) {
			$session = $this->plugin->getSessionManager()->get($victim->getName());
			if($session instanceof PlayerSession) {
				if($session->inDuel() and $session->getStatus() === PlayerSession::STATUS_PLAYING) {
					if($session->getDuel()->getStatus() === Duel::STATUS_PLAYING) {
						if($event->getFinalDamage() >= $victim->getHealth()) {
							$event->setCancelled(true);
							$victim->setFoodEnabled(false);
							$cause = $victim->getLastDamageCause();
							switch($cause === null ? EntityDamageEvent::CAUSE_CUSTOM : $cause->getCause()) {
								case EntityDamageEvent::CAUSE_ENTITY_ATTACK:
									if($cause instanceof EntityDamageByEntityEvent) {
										$damager = $cause->getDamager();
										if($damager instanceof Player) {
											$session->getDuel()->winner["val"] = $damager->getHealth();
											$message = TF::BOLD . TF::GOLD . $damager->getName() . TF::RESET . TF::YELLOW . " killed " . TF::BOLD . TF::AQUA . $victim->getName() . TF::RESET . TF::YELLOW . " with " . TF::RED . $session->getDuel()->winner["val"] / 2 . " <3's " . TF::GREEN . "left!";
											$session->getDuel()->winner["player"] = $damager->getName();
										} elseif($damager->getNameTag() !== null or $damager->getNameTag() !== "") {
											$message = TF::BOLD . TF::AQUA . $victim->getName() . TF::RESET . TF::YELLOW . " was killed by " . TF::BOLD . TF::GOLD . $victim->getName() . TF::RESET . TF::YELLOW . "!";
										}
									}
									break;
								case EntityDamageEvent::CAUSE_PROJECTILE:
									if($cause instanceof EntityDamageByEntityEvent) {
										$damager = $cause->getDamager();
										if($damager instanceof Player) {
											$session->getDuel()->winner["val"] = $damager->getHealth();
											$message = TF::BOLD . TF::GOLD . $damager->getName() . TF::RESET . TF::YELLOW . " shot " . TF::BOLD . TF::AQUA . $victim->getName() . TF::RESET . TF::YELLOW . " with " . TF::RED . $session->getDuel()->winner["val"] / 2 . " <3's " . TF::GREEN . "left!";
											$session->getDuel()->winner["player"] = $damager->getName();
										} elseif($damager->getNameTag() !== null or $damager->getNameTag() !== "") {
											$message = TF::BOLD . TF::AQUA . $victim->getName() . TF::RESET . TF::YELLOW . " was shot by " . TF::BOLD . TF::GOLD . $victim->getName() . TF::RESET . TF::YELLOW . "!";
										}
									}
									break;
								case EntityDamageEvent::CAUSE_FALL:
									$message = TF::BOLD . TF::AQUA . $victim->getName() . TF::RESET . TF::LIGHT_PURPLE . " thought they could soar like an eagle!";
									break;
								case EntityDamageEvent::CAUSE_DROWNING:
									$message = TF::BOLD . TF::AQUA . $victim->getName() . TF::RESET . TF::YELLOW . " thought they were a fish!";
									break;
								case EntityDamageEvent::CAUSE_FIRE:
								case EntityDamageEvent::CAUSE_FIRE_TICK:
									$message = TF::BOLD . TF::AQUA . $victim->getName() . TF::RESET . TF::GOLD . " was playing with fire!";
									break;
								case EntityDamageEvent::CAUSE_LAVA:
									$message = TF::BOLD . TF::AQUA . $victim->getName() . TF::RESET . TF::RED . " touched the hot stuff, how silly!";
									break;
								default:
									$message = TF::BOLD . TF::AQUA . $victim->getName() . TF::RESET . TF::YELLOW . " was killed!";
									break;
							}
							$session->getDuel()->broadcast($message);
							$session->getDuel()->handleDeath($victim);
						} else {

						}
					} else {
						$event->setCancelled(true);
					}
				} else {
					$event->setCancelled(true);
				}
			}
		} else {
			$event->setCancelled(true);
		}
	}

	public function onDeath(PlayerDeathEvent $event) {
		$event->setDeathMessage("");
		$victim = $event->getEntity();
		$event->setDrops([Item::get(0)]);
		$session = $this->plugin->getSessionManager()->get($victim->getName());
		if($session instanceof PlayerSession) {
			if($session->inDuel() and $session->getStatus() === PlayerSession::STATUS_PLAYING) {
				$session->getDuel()->handleDeath($victim);
			} else {
				$victim->teleport(Main::$spawnCoords);
				$session->removeDuel();
			}
		}
	}

	public function onQuit(PlayerQuitEvent $event) {
		$event->setQuitMessage("");
		$player = $event->getPlayer();
		//$this->plugin->lobbyBossBar->despawnFrom($player);
		$name = $player->getName();
		$session = $this->plugin->getSessionManager()->get($name);
		if($session instanceof PlayerSession) {
			if($session->inParty()) {
				$session->getParty()->removePlayer($name);
			}
			if($session->inDuel()) {
				$duel = $session->getDuel();
				$duel->broadcast(TF::LIGHT_PURPLE . $player->getName() . TF::GOLD . " left the duel!");
				$duel->removePlayer($player->getName());
			}
			$this->plugin->getSessionManager()->remove($name);
		}
	}

	public function onKick(PlayerKickEvent $event) {
		$event->setQuitMessage("");
		$player = $event->getPlayer();
		//$this->plugin->lobbyBossBar->despawnFrom($player);
		$name = $player->getName();
		$session = $this->plugin->sessionManager->get($name);
		if($session instanceof PlayerSession) {
			if($session->inParty()) {
				$session->getParty()->removePlayer($name);
			}
			if($session->inDuel()) {
				$duel = $session->getDuel();
				$duel->broadcast(TF::LIGHT_PURPLE . $player->getName() . TF::GOLD . " left the duel!");
				$duel->removePlayer($player->getName());
			}
			$this->plugin->getSessionManager()->remove($name);
		}
	}

	public function onDataReceive(DataPacketReceiveEvent $event) {
		/** @var CorePlayer $player */
		$player = $event->getPlayer();
		$pk = $event->getPacket();
		if($pk instanceof InteractPacket) {
			if($pk->action === InteractPacket::ACTION_DAMAGE) {
				$entity = $player->getLevel()->getEntity($event->getPacket()->target);
				if($entity instanceof HumanNPC) {
					if(!$player->isAuthenticated()) {
						$event->setCancelled(true);
						$player->sendTip(TF::RED . "Please authenticate first!");
					} else {
						$session = $this->plugin->getSessionManager()->get($player->getName());
						if($session instanceof PlayerSession) {
							if(!$session->inDuel()) {
								if($session->inParty()) {
									if($session->getParty()->isOwner($player)) {
										if($entity->getType() === Duel::TYPE_1V1) {
											if(count($session->getParty()->getPlayers()) === 2) {
												$players = [];
												foreach($session->getParty()->getPlayers() as $name => $uid) {
													$player = Utils::getPlayerByUUID($uid);
													if($player instanceof Player and $player->isOnline()) {
														$players[] = $player;
														if(count($players) === 2)
															break;
													} else {
														$player->sendMessage(TF::RED . "Cannot join duel due to {$name} being offline!");
														return;
													}
												}
												$arena = $this->plugin->getArenaManager()->find();
												if((!$arena instanceof Arena) or isset($this->plugin->duelManager->duels[$arena->getId()])) {
													$player->sendMessage(TF::RED . "Cannot find an open arena!");
													return;
												}
												$this->plugin->arenaManager->remove($arena->getId());
												$duel = new Duel($this->plugin, Duel::TYPE_1V1, $arena, $this->plugin->getKitManager()->findRandom(), false);
												$session->lastSelectedKit = null;
												foreach($players as $p) {
													$duel->addPlayer($p);
												}
												$this->plugin->duelManager->duels[$arena->getId()] = $duel;
											} else {
												$player->sendPopup(TF::GOLD . "You can only play 1v1's in a party that has two players!");
											}
										} elseif($entity->getType() === Duel::TYPE_2V2) {
											if(count($session->getParty()->getPlayers()) === 4) {
												$players = [];
												foreach($session->getParty()->getPlayers() as $name => $uid) {
													$player = Utils::getPlayerByUUID($uid);
													if($player instanceof Player and $player->isOnline()) {
														$players[] = $player;
														if(count($players) === 4)
															break;
													} else {
														$player->sendMessage(TF::RED . "Cannot join duel due to {$name} being offline!");
														return;
													}
												}
												$arena = $this->plugin->getArenaManager()->find();
												if(!($arena instanceof Arena) or isset($this->plugin->duelManager->duels[$arena->getId()])) {
													$player->sendMessage(TF::RED . "Cannot find an open arena!");
													return;
												}
												$this->plugin->arenaManager->remove($arena->getId());
												$duel = new Duel($this->plugin, Duel::TYPE_2V2, $arena, $this->plugin->getKitManager()->findRandom(), false);
												$session->lastSelectedKit = null;
												foreach($players as $p) {
													$duel->addPlayer($p);
												}
												$this->plugin->duelManager->duels[$arena->getId()] = $duel;
											} else {
												$player->sendPopup(TF::GOLD . "You can only play 2v2's in a party that has four players!!");
											}
										} else {
											$player->sendPopup(TF::GOLD . "You've managed to break something!");
										}
									} else {
										$player->sendMessage(TF::RED . "Only the party leader can join a duel!");
									}
								} else {
									$this->plugin->duelManager->findDuel($player, $entity->getType(), null, true);
								}
							} else {
								$player->sendPopup(TF::RED . "You're already in a duel!");
							}
						}
					}
				} elseif($entity instanceof Player) {
					$pSession = $sSession = $this->plugin->sessionManager->get($player->getName());
					$eSession = $rSession = $this->plugin->sessionManager->get($entity->getName());
					if($pSession instanceof PlayerSession and $eSession instanceof PlayerSession) {
						if($pSession->getStatus() === PlayerSession::STATUS_PLAYING and $eSession->getStatus() === PlayerSession::STATUS_PLAYING) {
							if(!(($pSession->inDuel() and $pSession->getDuel()->getType() === Duel::TYPE_FFA) and ($eSession->inDuel() and $eSession->getDuel()->getType() === Duel::TYPE_FFA)) and $pSession->getTeam() === $eSession->getTeam()) {
								$event->setCancelled(true);
							}
						} else {
							$event->setCancelled(true);
							if($player->getInventory() !== null) {
								/** @var Item $handItem */
								$handItem = $player->getInventory()->getItemInHand();
								if($handItem->getId() === Item::STICK and $handItem instanceof GUIItem) {
									if(!$sSession->inParty()) {
										if($sSession->hasRequest($entity->getName())) {
											if(!$sSession->inDuel()) {
												if(!$rSession->inDuel()) {
													$sSession->removeRequest($entity->getName());
													$arena = $this->plugin->getArenaManager()->find();
													if((!$arena instanceof Arena) or isset($this->plugin->duelManager->duels[$arena->getId()])) {
														$player->sendMessage(TF::RED . "Cannot find an open arena!");
														return true;
													}
													$this->plugin->arenaManager->remove($arena->getId());
													$entity->sendMessage(TF::GOLD . TF::BOLD . $player->getName() . TF::RESET . TF::GREEN . " has accepted your Duel request!");
													$player->sendMessage(TF::GREEN . "You have accepted a Duel request from " . TF::GOLD . TF::BOLD . $entity->getName() . TF::RESET . TF::GREEN . "!");
													$duel = new Duel($this->plugin, Duel::TYPE_1V1, $arena, ($rSession->lastSelectedKit instanceof Kit and !($rSession->lastSelectedKit instanceof RandomKit)) ? $eSession->lastSelectedKit : $this->plugin->getKitManager()->findRandom());
													$eSession->lastSelectedKit = null;
													$duel->addPlayer($player);
													$duel->addPlayer($entity);
													$this->plugin->duelManager->duels[$arena->getId()] = $duel;
													return true;
												} else {
													$player->sendMessage(TF::GOLD . $entity->getName() . TF::RED . " is currently in a duel, try again in a moment!");
													return true;
												}
											} else {
												$player->sendMessage(TF::RED . "You cannot accept duel request while in a duel!");
												return true;
											}
										} else {
											if(!$rSession->hasRequest($player->getName())) {
												$sSession->lastTapped = $entity;
												/** @var $handItem GUIItem */
												$handItem->handleClick($player, true);
												return true;
											} else {
												$player->sendMessage(TF::RED . "You've already sent " . TF::GOLD . $entity->getName() . TF::RED . " a request!");
												return true;
											}
										}
									} else {
										$player->sendMessage(TF::RED . "You cannot send or accept duel requests whilst in a party!");
									}
								}
							}
						}
					}
				}
			}
		}
		return false;
	}

	/**
	 * @param PlayerInteractEvent $event
	 *
	 * @priority LOWEST
	 */
	public function onInteract(PlayerInteractEvent $event) {
		$player = $event->getPlayer();
		/** @var Item $slot */
		$slot = clone $event->getItem();
		if($slot->getId() == Item::AIR) return;
		$session = $this->plugin->sessionManager->get($player->getName());
		if($session instanceof PlayerSession) {
			if($session->getStatus() == PlayerSession::STATUS_WAITING) {
				if($player->isAuthenticated()) {
					if($slot instanceof DuelKitRequestSelector) $event->setCancelled();

				} else {
					$player->sendTip(TF::RED . "Please authenticate first!");
					$event->setCancelled();
				}
			} else {
				if($player->getHealth() < $player->getMaxHealth()) {
					if($slot->getId() === Item::MUSHROOM_STEW) {
						$player->setHealth($player->getHealth() + 3);
						if($player->isSurvival()) {
							$player->getInventory()->setItemInHand(clone $this->air);
						}
					}
				}
			}
		} else {
			$event->setCancelled(true);
		}
	}

	//public function onHold(PlayerItemHeldEvent $event) {
	//	$player = $event->getPlayer();
	//	/** @var Item $slot */
	//	$slot = clone $event->getItem();
	//	if($slot->getId() == Item::AIR) return;
	//	$session = $this->plugin->sessionManager->get($player->getName());
	//	if($session instanceof PlayerSession) {
	//		if($session->getStatus() == PlayerSession::STATUS_WAITING) {
	//			foreach($this->plugin->getKitManager()->getSelectionItems() as $name => $display) {
	//				if($display->getId() == $slot->getId()) {
	//					$player->sendTip($slot->getCustomName());
	//					$player->sendPopup(TF::ITALIC . TF::GRAY . $this->plugin->getKitManager()->get($name)->getDescription());
	//				}
	//			}
	//		}
	//	} else {
	//		$event->setCancelled(true);
	//	}
	//}

	public function onExplode(EntityExplodeEvent $event) {
		$event->setBlockList([Block::get(0)]);
	}

	public function onLaunch(ProjectileLaunchEvent $event) {
		$player = $event->getEntity();
		if(!$player instanceof Player) return;
		$session = $this->plugin->sessionManager->get($player->getName());
		if($session instanceof PlayerSession) {
			if($session->getStatus() !== PlayerSession::STATUS_PLAYING) {
				$event->setCancelled(true);
			}
		}
	}

}