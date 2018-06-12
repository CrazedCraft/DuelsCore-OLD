<?php

namespace duels\duel;

use core\CorePlayer;
use core\entity\BossBar;
use core\game\team\TeamColors;
use core\Utils;
use duels\arena\Arena;
use duels\DuelsPlayer;
use duels\kit\Kit;
use duels\Main;
use pocketmine\inventory\PlayerInventory;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class Duel {
	const STATUS_WAITING = 0;
	const STATUS_PLAYING = 1;

	const OS_MOBILE = "mobile";
	const OS_WINDOWS = "windows";

	/** @var string[] */
	public $players = [];

	/** @var Kit|null */
	public $kit = null;

	public $winner;
	private $plugin;

	/** @var DuelType */
	private $type;

	private $status;
	private $arena;

	/** @var DuelCountdown|null */
	private $countdown = null;

	private $ended = false;

	/** @var Team[] */
	public $teams = [];

	/** @var BossBar */
	private $bossBar = null;

	private $os = self::OS_MOBILE;

	public function __construct(Main $plugin, DuelType $type, Arena $arena, Kit $kit) {
		$this->plugin = $plugin;
		$this->type = $type;
		$this->arena = $arena;
		$this->countdown = new DuelCountdown($this->plugin, $this);
		$this->status = self::STATUS_WAITING;
		$this->teams = [
			TeamColors::TEAM_ORANGE => new Team($this, "Gold", TeamColors::TEAM_ORANGE, TF::GOLD),
			TeamColors::TEAM_PURPLE => new Team($this, "Purple", TeamColors::TEAM_PURPLE, TF::DARK_PURPLE),
		];

		if($kit->getType() === Kit::TYPE_RANDOM) {
			$this->kit = $this->kit->getManager()->getRandomKit();
		} else {
			$this->kit = $kit;
		}
		//$this->bossBar = new BossBar();
	}

	public function getBossBar() : BossBar {
		return $this->bossBar;
	}

	public function hasEnded() {
		return $this->ended;
	}

	public function getType() : DuelType {
		return $this->type;
	}

	public function end() {
		if(!$this->ended) {
			$this->ended = true;
			if(isset($this->plugin)) {
				$this->plugin->getScheduler()->cancelTask($this->countdown->getTaskId());
				$this->plugin->arenaManager->addBack($this->arena);
				$this->plugin->duelManager->removeDuel($this->arena->getId());
			}
			if(!isset($this->winner["player"]) or $this->winner["player"] === "" or $this->winner["player"] === null) {
				$this->getWinner();
			}
			// $winner = $this->getWinner();
			foreach($this->players as $name => $uuid) {
				$p = Utils::getPlayerByUUID($uuid);
				if(!$p instanceof DuelsPlayer) {
					unset($this->players[$name]);
					continue;
				}
				if($this->type->getId() === DuelType::DUEL_TYPE_1V1 or $this->type->getId() === DuelType::DUEL_TYPE_FFA) {
					$p->sendMessage(TF::BOLD . TF::AQUA . $this->winner["player"] . TF::RESET . TF::GREEN . " won with " . TF::RED . $this->winner["val"] / 2 . " <3's " . TF::GREEN . "left!");
				} elseif($this->type->getId() === DuelType::DUEL_TYPE_2v2) {
					$winningTeam = null;
					foreach($this->teams as $team) {
						if($winningTeam instanceof Team) {
							if($team->getPlayerCount() > $winningTeam->getPlayerCount()) { // current team has more players
								$winningTeam = $team;
							} elseif($team->getPlayerCount() === $winningTeam->getPlayerCount()) { // decide winners from total health
								$health = 0;
								foreach($team->getPlayers() as $p) {
									$health += $p->getHealth();
								}

								$winningHealth = 0;
								foreach($winningTeam->getPlayers() as $p) {
									$winningHealth += $p->getHealth();
								}

								if($health > $winningHealth) {
									$winningTeam = $team;
								}
							}
						} else {
							$winningTeam = $team;
						}
					}
					$p->sendMessage(TF::BOLD . $winningTeam->getName() . TF::RESET . TF::GOLD . " team won the duel!");
					$p->sendMessage(TF::GREEN . "Winning kill by " . TF::BOLD . TF::AQUA . $this->winner["player"] . TF::RESET . TF::GREEN . " with " . TF::RED . $this->winner["val"] / 2 . " <3's " . TF::GREEN . "left!");
				}
				//$this->bossBar->despawnFrom($p);
				$p->setStatus("state.lobby");
				$p->removeAllEffects();
				$p->extinguish();
				$p->setHealth(20);
				$p->setFood(20);
				$p->teleport(Main::$spawnCoords);
				//$this->plugin->lobbyBossBar->spawnTo($p);
				if($p->getInventory() instanceof PlayerInventory) {
					$p->getInventory()->clearAll();
					$p->getArmorInventory()->clearAll();
					$p->getInventory()->sendContents($p);
					$p->getArmorInventory()->sendContents($p);
				}
				$p->setNameTag(TF::YELLOW . TF::clean($p->getName()));
				$p->removeDuel();
				if($p->isOnline()) $this->plugin->giveLobbyItems($p);
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
		foreach($this->players as $uuid) {
			$p = Utils::getPlayerByUUID($uuid);
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
		foreach($this->teams as $team) {
			foreach($team->getPlayers() as $name => $p) {
				if(!$p instanceof DuelsPlayer) {
					$this->removePlayer($name);
					continue;
				}
				$p->setStatus(CorePlayer::STATE_PLAYING);

				if($this->type->getId() === DuelType::DUEL_TYPE_2v2) {
					$p->setNameTag($team->getChatColor() . $p->getName());
				} else {
					$p->setNameTag(TF::RED . $p->getName());
				}

				if($this->type->getId() === DuelType::DUEL_TYPE_1V1) {
					$players = $this->getPlayers();
					unset($players[$p->getName()]);
					$p->sendMessage(TF::GREEN . "Duel against " . TF::BOLD . TF::GOLD . trim(implode(TF::RESET . TF::GRAY . ", " . TF::BOLD . TF::GOLD, array_keys($players)), TF::RESET . TF::GRAY . ", " . TF::BOLD . TF::GOLD) . TF::RESET . TF::GREEN . "!");
				} elseif($this->type->getId() === DuelType::DUEL_TYPE_2v2) {
					$teammates = $team->getPlayers();
					unset($teammates[$p->getName()]);
					$p->sendMessage(TF::GREEN . "You're on " . TF::BOLD . $team->getChatColor() . $team->getName() . TF::RESET . TF::GREEN . " team with " . TF::BOLD . TF::AQUA . trim(implode(",", $teammates), ","));
				} elseif($this->type->getId() === DuelType::DUEL_TYPE_FFA) {
					$players = $this->getPlayers();
					unset($players[$p->getName()]);
					$p->sendMessage(TF::GREEN . "FFA duel against " . TF::BOLD . TF::GOLD . trim(implode(TF::RESET . TF::GRAY . ", " . TF::BOLD . TF::GOLD, array_keys($players)), TF::RESET . TF::GRAY . ", " . TF::BOLD . TF::GOLD) . TF::RESET . TF::GREEN . "!");
				}
			}
		}
	}

	/**
	 * Teleport the players to their starting partitions
	 */
	public function teleportPlayers() {
		$i = 0;
		foreach($this->teams as $team) {
			foreach($team->getPlayers() as $p) {
				$p->teleport($p->getLevel()->getSafeSpawn($this->arena->getLocations()[$i]));
				$p->despawnFromAll();
				foreach($this->players as $uuid) {
					if($uuid !== $p->getUniqueId()->toString()) {
						$duelP = Utils::getPlayerByUUID($uuid);
						if($duelP instanceof DuelsPlayer) {
							$p->spawnTo($duelP);
						}
					}
				}
			}
			$i++;
		}
	}

	public function start() {
		$this->teleportPlayers();
		foreach($this->players as $uuid) {
			/** @var DuelsPlayer $p */
			$p = Utils::getPlayerByUUID($uuid);
			$this->kit->applyTo($p);
			$p->setFood(20);
		}
		$this->status = self::STATUS_PLAYING;
	}

	public function isJoinable() {
		if($this->status !== self::STATUS_WAITING) return;

		if($this->type->getId() === DuelType::DUEL_TYPE_FFA) {
			return $this->countdown->ffaWaitTime > 0 and count($this->players) < $this->type->getMaxPlayers();
		}

		return count($this->players) < $this->type->getMaxPlayers();
	}

	public function getPlayers() {
		if(!isset($this->players)) $this->end();
		return $this->players;
	}

	public function addPlayer(DuelsPlayer $player) {
		if($player->hasDuel()) {
			$duel = $player->getDuel();
			$duel->broadcast(TF::LIGHT_PURPLE . $player->getName() . TF::GOLD . " left the duel!");
			$duel->handleDeath($player);
		}
		$player->setDuel($this);
		//$this->plugin->lobbyBossBar->despawnFrom($player);
		//$this->bossBar->spawnTo($player);
		$this->players[$player->getName()] = $player->getUniqueId()->toString();
		$player->setStatus(CorePlayer::STATE_PLAYING);
		$player->sendMessage(TF::YELLOW . "You have joined the queue for " . $this->kit->getDisplayName() . TF::RESET . TF::YELLOW . " kit");
		$player->getInventory()->clearAll();
		$player->setHealth(20);
		if($this->type->getId() === DuelType::DUEL_TYPE_1V1) {
			foreach($this->teams as $team) {
				if($team->getPlayerCount() < 1) {
					$team->addPlayer($player);
					break;
				}
			}
		} elseif($this->type->getId() === DuelType::DUEL_TYPE_2v2) {
			foreach($this->teams as $team) {
				if($team->getPlayerCount() < 2) {
					$team->addPlayer($player);
					break;
				}
			}
		}
	}

	/**
	 * @param DuelsPlayer[] $players
	 * @param bool  $group  Should we try and place these players on the same team?
	 */
	public function addPlayers(array $players, bool $group = true) {
		if($group) {
			foreach($players as $player) {
				if($player->hasDuel()) {
					$duel = $player->getDuel();
					$duel->broadcast(TF::LIGHT_PURPLE . $player->getName() . TF::GOLD . " left the duel!");
					$duel->handleDeath($player);
				}
				$player->setDuel($this);
				//$this->plugin->lobbyBossBar->despawnFrom($player);
				//$this->bossBar->spawnTo($player);
				$this->players[$player->getName()] = $player->getUniqueId()->toString();
				$player->setStatus(CorePlayer::STATE_PLAYING);
				$player->sendMessage(TF::YELLOW . "You have joined the queue for " . $this->kit->getDisplayName() . TF::RESET . TF::YELLOW . " kit");
				$player->getInventory()->clearAll();
				$player->setHealth(20);
			}

			if(($count = count($players)) <= 2 ) {
				foreach($this->teams as $team) {
					if($count <= (2 - $team->getPlayerCount())) {
						foreach($players as $p) {
							$team->addPlayer($p);
						}
						return;
					}
				}
			} else {
				/** @var Team[] $teams */
				$teams = usort($this->teams, function($a, $b) {
					assert($a instanceof Team and $b instanceof Team);
					return ($a->getPlayerCount() < $b->getPlayerCount()) ? -1 : 1;
				}); // sort teams by the least amount of players

				foreach($teams as $team) {
					foreach($players as $key => $p) {
						if($team->getPlayerCount() < 2) {
							$team->addPlayer($p);
							unset($players[$key]);
						}
					}
					return;
				}
			}

		} else {
			foreach($players as $p) {
				$this->addPlayer($p);
			}
		}
	}

	public function removePlayer($name) {
		unset($this->players[$name]);
		$p = $this->plugin->getServer()->getPlayer($name);
		if($p instanceof CorePlayer) {
			$p->setStatus(CorePlayer::STATE_LOBBY);
		}
	}

	public function broadcast($message) {
		foreach($this->players as $uuid) {
			$p = Utils::getPlayerByUUID($uuid);
			$p->sendMessage($message);
		}
	}

	public function broadcastTip($message) {
		foreach($this->players as $uuid) {
			$p = Utils::getPlayerByUUID($uuid);
			$p->sendPopup($message);
		}
	}

	public function handleDeath(DuelsPlayer $victim) {
		//$this->bossBar->despawnFrom($victim);
		$victim->removeAllEffects();
		$victim->setHealth(20);
		$victim->setFood(20);
		$victim->extinguish();
		$victim->teleport(Main::$spawnCoords);
		if($victim->getInventory() instanceof PlayerInventory) $victim->getInventory()->clearAll();
		$victim->getInventory()->sendContents($victim);
		$victim->getArmorInventory()->sendContents($victim);
		$victim->removeDuel();
		$victim->setNameTag(TF::YELLOW . TF::clean($victim->getName()));
		if($victim->isOnline()) $this->plugin->giveLobbyItems($victim);;
		if($this->type->getId() === DuelType::DUEL_TYPE_1V1) {
			$this->removePlayer($victim->getName());
			$this->end();
		} elseif($this->type->getId() === DuelType::DUEL_TYPE_2v2) {
			unset($this->players[$victim->getName()]);
			foreach($this->teams as $key => $team) {
				$team->removePlayer($victim->getName());
				unset($this->players[$victim->getName()]);
				if($team->getPlayerCount() <= 0) {
					$this->end();
					break;
				}
			}
		} elseif($this->type->getId() === DuelType::DUEL_TYPE_FFA) {
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
		$isWindows = in_array($os, [CorePlayer::OS_WIN10, CorePlayer::OS_WIN32]);
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