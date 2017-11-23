<?php

/**
 * Duels duel request class
 *
 * Created on Mar 6, 2016 at 4:19:00 PM
 *
 * @author Jack
 */

namespace duels\session;

use duels\DuelsPlayer;
use pocketmine\scheduler\PluginTask;
use pocketmine\Player;

use duels\Main;

class DuelRequest extends PluginTask {

	/** @var Main */
	private $plugin;

	/** @var DuelsPlayer */
	public $to;

	/** @var Player */
	public $from;

	public $active = true;

	public function __construct(Main $plugin, DuelsPlayer $to, DuelsPlayer $from) {
		parent::__construct($plugin);
		$this->plugin = $plugin;
		$this->from = $from;
		$this->to = $to;
		$this->setHandler($plugin->getServer()->getScheduler()->scheduleDelayedTask($this, 20 * 60));
	}

	/**
	 * @return Main;
	 */
	public function getPlugin() {
		return $this->plugin;
	}

	public function onRun($tick) {
		if($this->active) {
			$this->from->sendMessage("Party request to " . $this->to->getPlayer()->getName() . " has timed out");
			$this->to->getPlayer()->sendMessage("Party request from " . $this->from->getName() . " has timed out");
			$this->to->removeRequest($this->from);
			$this->plugin->getServer()->getScheduler()->cancelTask($this->getTaskId());
		}
	}

	public function onCancel() {
		$this->active = false;
	}

}