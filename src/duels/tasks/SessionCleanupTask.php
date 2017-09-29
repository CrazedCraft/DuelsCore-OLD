<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 6/09/2016
 * Time: 8:01 PM
 */

namespace duels\tasks;

use duels\Main;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;

class SessionCleanupTask extends PluginTask {

	/** @var Main */
	protected $plugin;

	public function __construct(Main $plugin) {
		parent::__construct($plugin);
		$this->plugin = $plugin;
		$plugin->getServer()->getScheduler()->scheduleRepeatingTask($this, 20 * 60 * 5);
	}

	public function onRun($currentTick) {
		$sesMgr = $this->plugin->getSessionManager();
		foreach($sesMgr->getAll() as $key => $session) {
			if($session->isActive() and !$session->getPlayer() instanceof Player or ($session->getPlayer() instanceof Player and !$session->getPlayer()->isOnline())) $sesMgr->remove($key);
		}
	}

}