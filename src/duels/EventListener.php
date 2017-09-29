<?php

namespace duels;

use core\CorePlayer;
use core\gui\item\GUIItem;
use duels\arena\Arena;
use duels\duel\Duel;
use duels\gui\item\duel\DuelKitRequestSelector;
use duels\gui\item\kit\KitSelector;
use duels\kit\Kit;
use duels\kit\RandomKit;
use duels\session\PlayerSession;
use pocketmine\block\Block;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\item\Item;
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

	/**
	 * @param PlayerCreationEvent $event
	 *
	 * @priority MONITOR
	 */
	public function onCreation(PlayerCreationEvent $event) {
		$event->setPlayerClass(DuelsPlayer::class);
	}

	public function onQuery(QueryRegenerateEvent $event) {
		$event->setServerName(Main::translateColors("&1C&ar&ea&6z&9e&5d&fC&7r&6a&cf&dt &l&6Duels&r"));
	}

	public function onExhaust(PlayerExhaustEvent $event) {
		$player = $event->getPlayer();
		/** @var PlayerSession $session */
		$session = $this->plugin->sessionManager->get($player->getName());
		if($session instanceof PlayerSession) {
			if($session->getStatus() !== PlayerSession::STATUS_PLAYING) {
				$event->setCancelled(true);
			}
		}
	}

	public function onLogin(PlayerLoginEvent $event) {
		$event->getPlayer()->setSpawn(Main::$spawnCoords);
		$event->getPlayer()->teleport(Main::$spawnCoords);
	}

	public function onJoin(PlayerJoinEvent $event) {
		$event->setJoinMessage("");
		$player = $event->getPlayer();
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
		if($victim instanceof CorePlayer) {
			$session = $this->plugin->getSessionManager()->get($victim->getName());
			if($session instanceof PlayerSession) {
				if($session->inDuel() and $session->getStatus() === PlayerSession::STATUS_PLAYING and $victim->getState() === CorePlayer::STATE_PLAYING) {
					if($event->getFinalDamage() >= $victim->getHealth()) {
						$event->setCancelled(true);
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
								//$message = TF::BOLD . TF::AQUA . $victim->getName() . TF::RESET . TF::LIGHT_PURPLE . " thought they could soar like an eagle!";
								return;
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
						if($event->getCause() === EntityDamageEvent::CAUSE_FALL) {
							$event->setCancelled(true);
							return;
						}
					}
				} elseif($event instanceof EntityDamageByEntityEvent) {
					$target = $event->getEntity();
					$attacker = $event->getDamager();
					if($attacker instanceof CorePlayer and $attacker->getState() === CorePlayer::STATE_LOBBY and $target instanceof CorePlayer and $target->getState() === CorePlayer::STATE_LOBBY) {
						$item = $attacker->getInventory()->getItemInHand();
						if($item->getId() === Item::STICK and $item instanceof GUIItem) {
							$aSession = $this->plugin->sessionManager->get($attacker->getName());
							$tSession = $this->plugin->sessionManager->get($target->getName());
							if($aSession instanceof PlayerSession and $tSession instanceof PlayerSession) {
								if(!$aSession->inParty()) {
									if($aSession->hasRequest($target->getName())) {
										$aSession->removeRequest($target->getName());
										$arena = $this->plugin->getArenaManager()->find();
										if((!$arena instanceof Arena) or isset($this->plugin->duelManager->duels[$arena->getId()])) {
											$attacker->sendMessage(TF::RED . "Cannot find an open arena!");
											return false;
										}
										$this->plugin->arenaManager->remove($arena->getId());
										$target->sendMessage(TF::GOLD . TF::BOLD . $attacker->getName() . TF::RESET . TF::GREEN . " has accepted your Duel request!");
										$attacker->sendMessage(TF::GREEN . "You have accepted a Duel request from " . TF::GOLD . TF::BOLD . $attacker->getName() . TF::RESET . TF::GREEN . "!");
										$duel = new Duel($this->plugin, Duel::TYPE_1V1, $arena, ($tSession->lastSelectedKit instanceof Kit and !($tSession->lastSelectedKit instanceof RandomKit)) ? $tSession->lastSelectedKit : $this->plugin->getKitManager()->findRandom());
										$tSession->lastSelectedKit = null;
										$duel->addPlayer($target);
										$duel->addPlayer($attacker);
										$this->plugin->duelManager->duels[$arena->getId()] = $duel;
										return false;
									} else {
										if(!$tSession->hasRequest($attacker->getName())) {
											$aSession->lastTapped = $target;
											/** @var $handItem GUIItem */
											$item->handleClick($attacker, true);
											return false;
										} else {
											$attacker->sendMessage(TF::RED . "You've already sent " . TF::GOLD . $target->getName() . TF::RED . " a request!");
											return false;
										}
									}
								} else {
									$attacker->sendMessage(TF::RED . "You cannot send or accept duel requests whilst in a party!");
								}
							} else {
								$event->setCancelled(true);
							}
						} else {
							$event->setCancelled(true);
						}
					} else {
						$event->setCancelled(true);
					}
				} elseif($event->getCause() === EntityDamageEvent::CAUSE_VOID) {
					$victim->kill();
					$event->setCancelled(true);
				} else {
					$event->setCancelled(true);
				}
			} else {
				$victim->kick("Invalid session");
			}
		} else {
			$event->setCancelled(true);
		}
		return true;
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

	/**
	 * @param PlayerInteractEvent $event
	 *
	 * @priority LOWEST
	 */
	public function onInteract(PlayerInteractEvent $event) {
		$player = $event->getPlayer();
		/** @var Item $slot */
		$slot = clone $player->getInventory()->getItemInHand();
		if($slot->getId() === Item::AIR)
			return;

		if($slot instanceof DuelKitRequestSelector) {
			$event->setCancelled();
			return;
		} elseif($slot instanceof KitSelector) {
			if($player->isAuthenticated()) {
				$event->setCancelled(false);
			} else {
				$player->sendTip(TF::RED . "Please authenticate first!");
				$event->setCancelled();
			}
			return;
		}

		$session = $this->plugin->sessionManager->get($player->getName());
		if($session instanceof PlayerSession) {
			if($session->getStatus() !== PlayerSession::STATUS_WAITING) {
				if($slot->getId() === Item::MUSHROOM_STEW and $player->getHealth() < $player->getMaxHealth()) {
					$player->setHealth($player->getHealth() + 3);
					if($player->isSurvival()) {
						$player->getInventory()->setItemInHand(clone $this->air);
					}
				}
			}
		} else {
			$event->setCancelled(true);
		}
	}

	public function onExplode(EntityExplodeEvent $event) {
		$event->setBlockList([Block::get(0)]);
	}

	public function onLaunch(ProjectileLaunchEvent $event) {
		$player = $event->getEntity();
		if(!$player instanceof Player)
			return;
		$session = $this->plugin->sessionManager->get($player->getName());
		if($session instanceof PlayerSession) {
			if($session->getStatus() !== PlayerSession::STATUS_PLAYING) {
				$event->setCancelled(true);
			}
		}
	}

}