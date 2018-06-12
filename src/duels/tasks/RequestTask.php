<?php

namespace duels\tasks;

use duels\Main;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat as TF;

class RequestTask extends Task {

	public $target;
	public $from;
	private $plugin;

	public function __construct(Main $plugin, Player $target, Player $from) {
		$plugin->getScheduler()->scheduleDelayedTask($this, 20 * 60 * 2));
		$this->plugin = $plugin;
		$this->target = $target;
		$this->from = $from;
	}

	public function onRun($tick) {
		$session = $this->plugin->sessionManager->get($this->target->getName());
		if($session instanceof PlayerSession) {
			$session->removeRequest($this->from->getName());
		}
		$this->target->sendMessage(TF::GOLD . "Duel request from " . TF::BOLD . TF::AQUA . $this->from->getName() . TF::RESET . TF::GOLD . " has timed out!");
		$this->from->sendMessage(TF::GOLD . "Duel request to " . TF::BOLD . TF::AQUA . $this->target->getName() . TF::RESET . TF::GOLD . " has timed out!");
		$this->plugin->getServer()->getScheduler()->cancelTask($this->getTaskId());
	}

}
