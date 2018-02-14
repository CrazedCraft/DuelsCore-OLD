<?php

namespace duels\duel;

use duels\Main;
use duels\session\PlayerSession;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat as TF;

class DuelCountdown extends PluginTask {

	/** @var Main */
	private $plugin;

	/** @var Duel */
	private $duel;

	/** @var int */
	private $waitTime = 5;

	/** @var int */
	public $ffaWaitTime = 60; // give players 60 seconds to join a public ffa duel before starting

	/** @var int */
	private $duelTime = 600;

	/** @var bool */
	private $hasTeleported = false;

	/** @var bool */
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
						if($this->duel->getType()->getId() === DuelType::DUEL_TYPE_FFA and count($this->duel->players) >= $this->duel->getType()->getMinPlayers()) {
							$this->duel->broadcastTip(TF::LIGHT_PURPLE . "FFA duel will begin in " . TF::LIGHT_PURPLE . $this->ffaWaitTime . TF::LIGHT_PURPLE . " second(s)");
							$this->ffaWaitTime--;
						} else {
							//$this->duel->getBossBar()->setText(TF::GRAY . "Waiting for players...");
							$this->duel->broadcastTip(TF::LIGHT_PURPLE . "Waiting for players (" . TF::GREEN . count($this->duel->getPlayers()) . TF::LIGHT_PURPLE . "/" . TF::GREEN . $this->duel->getType()->getMaxPlayers() . TF::LIGHT_PURPLE . ")");
						}
					}
				} else {
					$this->duel->start();
				}
			} elseif($this->duelTime >= 0) {
				if(($this->duel->getType()->getId() === DuelType::DUEL_TYPE_1V1 or $this->duel->getType()->getId() === DuelType::DUEL_TYPE_FFA) and count($this->duel->players) <= 1) {
					$this->duel->end();
					return;
				} elseif($this->duel->getType()->getId() === DuelType::DUEL_TYPE_2v2) {
					foreach($this->duel->teams as $team) {
						$count = 0;
						foreach($team as $key => $player) {
							if($player instanceof Player and $player->isOnline()) {
								$count++;
							}
						}
						if($count < 1) {
							$this->duel->end();
							return;
						}
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