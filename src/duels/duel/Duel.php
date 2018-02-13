<?php

namespace duels\duel;

use core\CorePlayer;
use core\entity\BossBar;
use duels\arena\Arena;
use duels\kit\Kit;
use duels\Main;
use duels\session\PlayerSession;
use pocketmine\inventory\PlayerInventory;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class Duel {

	const TYPE_1V1 = "1v1";
	const TYPE_2V2 = "2v2";
	const TYPE_FFA = "FFA";
	const STATUS_WAITING = 0;
	const STATUS_PLAYING = 1;
	const OS_MOBILE = "mobile";
	const OS_WINDOWS = "windows";
	public $players = [];
	public $kit = null;
	public $winner;
	private $plugin;
	private $type = self::TYPE_1V1;
	private $status;
	private $arena;
	private $countdown = null;
	private $ended = false;
	public $teams = [];

	/** @var BossBar */
	private $bossBar = null;

	private $os = self::OS_MOBILE;

	public function __construct(Main $plugin, $type, Arena $arena, Kit $kit) {
		$this->plugin = $plugin;
		$this->type = $type;
		$this->arena = $arena;
		$this->countdown = new DuelCountdown($this->plugin, $this);
		$this->status = self::STATUS_WAITING;
		$this->teams = [[], []];
		$this->kit = $kit;
		//$this->bossBar = new BossBar();
	}

	public function getBossBar() : BossBar {
		return $this->bossBar;
	}

	public function hasEnded() {
		return $this->ended;
	}

	public function getType() {
		return $this->type;
	}

	public function end() {
		if(!$this->ended) {
			$this->ended = true;
			if(isset($this->plugin)) {
				$this->plugin->getServer()->getScheduler()->cancelTask($this->countdown->getTaskId());
				$this->plugin->arenaManager->addBack($this->arena);
				$this->plugin->duelManager->removeDuel($this->arena->getId());
			}
			if(!isset($this->winner["player"]) or $this->winner["player"] === "" or $this->winner["player"] === null) {
				$this->getWinner();
			}
			// $winner = $this->getWinner();
			foreach($this->players as $name => $p) {
				if(!$p instanceof Player) {
					unset($this->players[$name]);
					continue;
				}
				if($this->type === self::TYPE_1V1 or $this->type === self::TYPE_FFA) {
					$p->sendMessage(TF::BOLD . TF::AQUA . $this->winner["player"] . TF::RESET . TF::GREEN . " won with " . TF::RED . $this->winner["val"] / 2 . " <3's " . TF::GREEN . "left!");
				}
				elseif($this->type === self::TYPE_2V2) {
					$session = $this->plugin->sessionManager->get($p->getName());
					if($session instanceof PlayerSession) {
						$winningTeam = ($session->getTeam() === "0" ? TF::GOLD . "Orange" : TF::DARK_PURPLE . "Purple");
						$p->sendMessage(TF::BOLD . $winningTeam . TF::RESET . TF::GOLD . " team won the duel!");
						$p->sendMessage(TF::GREEN . "Winning kill by " . TF::BOLD . TF::AQUA . $this->winner["player"] . TF::RESET . TF::GREEN . " with " . TF::RED . $this->winner["val"] / 2 . " <3's " . TF::GREEN . "left!");
					}
				}
				//$this->bossBar->despawnFrom($p);
				$p->setStatus("state.lobby");
				$p->removeAllEffects();
				$p->extinguish();
				$p->setHealth(20);
				$p->setFood(20);
				$p->teleport(Main::$spawnCoords);
				$p->getInventory()->sendContents($p);
				$p->getInventory()->sendArmorContents($p);
				//$this->plugin->lobbyBossBar->spawnTo($p);
				if($p->getInventory() instanceof PlayerInventory) {
					$p->getInventory()->clearAll();
					$p->getInventory()->sendArmorContents($p);
				}
				$session = $this->plugin->sessionManager->get($p->getName());
				$p->setNameTag(TF::YELLOW . TF::clean($p->getName()));
				if($session instanceof PlayerSession) $session->removeDuel();
				if($p instanceof Player and $p->isOnline()) $this->plugin->giveLobbyItems($p);
			}
			unset($this->type);
			unset($this->status);
			unset($this->players);
			//unset($this->plugin);
		}
	}

	public function getWinner() {
		$highest = 0;
		$winner = null;
		foreach($this->players as $p) {
			if($p->getHealth() > $highest) {
				$winner = $p;
				$highest = $p->getHealth();
			}
		}
		if($winner instanceof Player) {
			$this->winner["player"] = $winner->getName();
			$this->winner["val"] = $winner->getHealth();
		} else {
			$this->winner["player"] = "";
			$this->winner["val"] = "0";
		}
	}

	public function getArena() {
		if(!isset($this->arena)) $this->end();
		return $this->arena;
	}

	public function getStatus() {
		if(!isset($this->status)) {
			$this->end();
			return self::STATUS_WAITING;
		}
		return $this->status;
	}

	public function setStatus($status) {
		$this->status = $status;
	}

	public function getKit() {
		return $this->kit;
	}

	public function setKit(Kit $kit) {
		$this->kit = $kit;
	}

	public function countdown() {
		$this->teleportPlayers();
		foreach($this->teams as $key => $t) {
			/** @var Player $p */
			foreach($t as $p) {
				$session = $this->plugin->getSessionManager()->get($p->getName());
				if(!$session instanceof PlayerSession) {
					$this->removePlayer($p->getName());
					continue;
				}
				$session->setStatus(PlayerSession::STATUS_COUNTDOWN);
				if(!$this->kit instanceof Kit) {
					$this->kit = $this->plugin->kitManager->getRandomKit();
				}
				if($this->type === Duel::TYPE_2V2) {
					if($session->getTeam() === "0")
						$p->setNameTag(TF::GOLD . $p->getName());
					if($session->getTeam() === "1")
						$p->setNameTag(TF::DARK_PURPLE . $p->getName());
				} else {
					$p->setNameTag(TF::RED . $p->getName());
				}
				if($this->type === self::TYPE_1V1) {
					$p->sendMessage(TF::GREEN . "Duel against " . TF::BOLD . TF::GOLD . array_rand($this->teams[($session->getTeam() === "0" ? "1" : "0")]) . TF::GREEN . "!");
				} elseif($this->type === self::TYPE_2V2) {
					$teammate = $t;
					unset($teammate[$p->getName()]);
					$p->sendMessage(TF::GREEN . "You're on " . TF::BOLD . ($session->getTeam() === "0" ? TF::GOLD . "orange" : TF::DARK_PURPLE . "purple") . TF::RESET . TF::GREEN . " team with " . TF::BOLD . TF::AQUA . array_rand($teammate));
				} elseif($this->type === self::TYPE_FFA) {
					$p->sendMessage(TF::GREEN . "FFA duel against " . TF::BOLD . TF::GOLD . implode(TF::RESET . TF::GRAY . ", " . TF::BOLD . TF::GOLD, array_keys($this->players)) . TF::GREEN . "!");
				}
				$p->despawnFromAll();
				foreach($this->players as $duelP) {
					$p->spawnTo($duelP);
				}
				$p->getInventory()->sendContents($p);
			}
		}
	}

	/**
	 * Teleport the players to their starting partitions
	 */
	public function teleportPlayers() {
		$i = 0;
		foreach($this->teams as $key => $t) {
			/** @var Player $p */
			foreach($t as $p) {
				$p->teleport($p->getLevel()->getSafeSpawn($this->arena->getLocations()[$i]), $p->yaw, $p->pitch);
				$p->despawnFromAll();
				foreach($this->players as $duelP) {
					$p->spawnTo($duelP);
				}
			}
			$i++;
		}
	}

	public function start() {
		$this->teleportPlayers();
		foreach($this->players as $p) {
			if($this->kit->getType() === Kit::TYPE_RANDOM) {
				$this->kit = $this->kit->getManager()->getRandomKit();
			}
			$this->kit->applyTo($p);
			foreach($this->players as $opponent) {
				if($p->getName() === $opponent->getName()) continue;
				$p->despawnFrom($opponent);
				$p->spawnTo($opponent);
				$p->getInventory()->sendContents($opponent);
			}
			$p->setFood(20);
			$this->plugin->sessionManager->get($p->getName())->setStatus(PlayerSession::STATUS_PLAYING);
		}
		$this->status = self::STATUS_PLAYING;
	}

	public function isJoinable() {
		if($this->status !== self::STATUS_WAITING) return;
		if($this->type === self::TYPE_1V1) {
			return !(count($this->players) >= 2);
		} elseif($this->type === self::TYPE_2V2) {
			return !(count($this->players) >= 4);
		} elseif($this->type === self::TYPE_FFA) {
			return false;
		}
		return false;
	}

	public function getPlayers() {
		if(!isset($this->players)) $this->end();
		return $this->players;
	}

	public function addPlayer(CorePlayer $player) {
		/** @var $session PlayerSession */
		if(!($session = $this->plugin->sessionManager->get($player->getName())) instanceof PlayerSession) $player->kick(TF::RED . "Invalid session, please rejoin to enjoy duels!", false);
		if($session->inDuel()) {
			$duel = $session->getDuel();
			$duel->broadcast(TF::LIGHT_PURPLE . $player->getName() . TF::GOLD . " left the duel!");
			$duel->handleDeath($player);
		}
		$session->setDuel($this);
		//$this->plugin->lobbyBossBar->despawnFrom($player);
		//$this->bossBar->spawnTo($player);
		$this->players[$player->getName()] = $player;
		$player->setStatus(CorePlayer::STATE_PLAYING);
		$session->setStatus(PlayerSession::STATUS_WAITING);
		$player->sendMessage(TF::YELLOW . "You have joined the queue for " . $this->kit->getDisplayName() . TF::RESET . TF::YELLOW . " kit");
		$player->getInventory()->clearAll();
		$player->setHealth(20);
		if($this->type === self::TYPE_1V1) {
			if(count($this->teams["0"]) < 1) {
				$this->teams["0"][$player->getName()] = $player;
				return $session->setTeam("0");
			} elseif(count($this->teams["1"]) < 1) {
				$this->teams["1"][$player->getName()] = $player;
				return $session->setTeam("1");
			}
		} elseif($this->type === self::TYPE_2V2) {
			if(count($this->teams["0"]) < 2) {
				$this->teams["0"][$player->getName()] = $player;
				return $session->setTeam("0");
			} elseif(count($this->teams["1"]) < 2) {
				$this->teams["1"][$player->getName()] = $player;
				return $session->setTeam("1");
			}
		} elseif($this->type === self::TYPE_FFA) {
			if(count($this->teams["0"]) < count($this->teams["1"])) {
				$this->teams["0"][$player->getName()] = $player;
				return $session->setTeam("0");
			} else {
				$this->teams["1"][$player->getName()] = $player;
				return $session->setTeam("1");
			}
		}
		return null;
	}

	public function removePlayer($name) {
		unset($this->players[$name]);
		$p = $this->plugin->getServer()->getPlayer($name);
		if($p instanceof CorePlayer) {
			$p->setStatus(CorePlayer::STATE_LOBBY);
		}
	}

	public function broadcast($message) {
		foreach($this->players as $p) {
			$p->sendMessage($message);
		}
	}

	public function broadcastTip($message) {
		foreach($this->players as $p) {
			$p->sendPopup($message);
		}
	}

	public function handleDeath(CorePlayer $victim) {
		//$this->bossBar->despawnFrom($victim);
		$victim->removeAllEffects();
		$victim->setHealth(20);
		$victim->setFood(20);
		$victim->getInventory()->sendContents($victim);
		$victim->getInventory()->sendArmorContents($victim);
		$victim->extinguish();
		$victim->teleport(Main::$spawnCoords);
		if($victim->getInventory() instanceof PlayerInventory) $victim->getInventory()->clearAll();
		$session = $this->plugin->sessionManager->get($victim->getName());
		$session->removeDuel();
		$victim->setNameTag(TF::YELLOW . TF::clean($victim->getName()));
		if($victim instanceof Player and $victim->isOnline()) $this->plugin->giveLobbyItems($victim);;
		if($this->type === self::TYPE_1V1) {
			$this->removePlayer($victim->getName());
			$this->end();
		} elseif($this->type === self::TYPE_2V2) {
			unset($this->players[$victim->getName()]);
			foreach($this->teams as $key => $t) {
				unset($this->teams[$key][$victim->getName()]);
				unset($this->players[$victim->getName()]);
				if(count($this->teams[$key]) <= 0) {
					$this->end();
					break;
				}
			}
		} elseif($this->type === self::TYPE_FFA) {
			$this->removePlayer($victim->getName());
			if(count($this->players) <= 1) {
				$this->end();
			}
		}
		//$this->plugin->lobbyBossBar->spawnTo($victim);
	}

	public function listNames(array $array) {
		$string = "";
		foreach($array as $name) {
			$string .= ($string === "" ? $name : ", " . $name);
		}
	}

	public function getOs() {
		return $this->os;
	}

	public function setOs(string $os = "") {
		$this->os = $os;
	}

	public function matchesOS(int $os) {
		$isWindows = in_array($os, [Player::OS_WIN10, Player::OS_WIN32]);
		if($this->os === self::OS_MOBILE) {
			return !$isWindows;
		} else {
			return $isWindows;
		}
	}

	public function __destruct() {
		$this->end();
	}

}