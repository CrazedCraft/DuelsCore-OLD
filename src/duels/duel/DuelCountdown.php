<?php

namespace duels\duel;

use duels\Main;
use duels\session\PlayerSession;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat as TF;

class DuelCountdown extends PluginTask {

	private $plugin;

	private $duel;

	private $waitTime = 5;

	private $duelTime = 600;

	private $hasTeleported = false;

	private $isActive = true;

	public function __construct(Main $plugin, Duel $duel) {
		parent::__construct($plugin);
		$this->setHandler($plugin->getServer()->getScheduler()->scheduleRepeatingTask($this, 20));
		$this->plugin = $plugin;
		$this->duel = $duel;
	}

	public function onRun($tick) {
		if($this->isActive) {
			if($this->duel->getStatus() === Duel::STATUS_WAITING) {
				if($this->waitTime >= 0) {
					if(!$this->duel->isJoinable()) {
						if(!$this->hasTeleported) {
							$this->duel->countdown();
							$this->hasTeleported = true;
						}
						//$this->duel->getBossBar()->setText(TF::GREEN . "Duel begins in " . $this->waitTime . "...");
						$this->duel->broadcastTip(TF::LIGHT_PURPLE . "Duel will begin in " . TF::BOLD . TF::YELLOW . (string)$this->waitTime . TF::RESET);
						$this->waitTime--;
					} else {
						//$this->duel->getBossBar()->setText(TF::GRAY . "Waiting for players...");
						$this->duel->broadcastTip(TF::LIGHT_PURPLE . "Waiting for players (" . TF::GREEN . count($this->duel->getPlayers()) . TF::LIGHT_PURPLE . "/" . TF::GREEN . ($this->duel->getType() === Duel::TYPE_1V1 ? "2" : "4") . TF::LIGHT_PURPLE . ")");
					}
				} else {
					$this->duel->start();
				}
			} elseif($this->duelTime >= 0) {
				if(($this->duel->getType() === Duel::TYPE_1V1 or $this->duel->getType() === Duel::TYPE_FFA) and count($this->duel->players) <= 1) {
					$this->duel->end();
					return;
				} elseif($this->duel->getType() === Duel::TYPE_2V2) {
					$teams = [];
					foreach($this->duel->getPlayers() as $player) {
						$ses = $this->plugin->getSessionManager()->get($player->getName());
						if($ses instanceof PlayerSession) {
							if($ses->inTeam()) {
								$teams[$ses->getTeam()] = $player;
							}
						}
					}
					foreach($teams as $team) if(count($team) < 1)  {
						$this->duel->end();
						return;
					}
				}
				$time = Main::printSeconds($this->duelTime);
				//$this->duel->getBossBar()->setText(TF::GRAY . $time ." | Kit: " . TF::clean($this->duel->getKit()->getName()));
				//$this->duel->getBossBar()->setProgress($this->waitTime / 600);
				$this->duel->broadcastTip(TF::GOLD . "Duel will end in " . TF::BOLD . TF::GREEN . $time . TF::RESET);
				$this->duelTime--;
			} else {
				$this->end();
			}
		}
	}

	public function end($active = true) {
		if($this->isActive and $active) $this->duel->end();
		$this->isActive = false;
	}

	public function onCancel() {
		$this->end(false);
	}

}