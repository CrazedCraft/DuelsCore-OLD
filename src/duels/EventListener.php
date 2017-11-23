<?php

/**
 * Duels event listener
 *
 * Created on Mar 6, 2016 at 4:19:00 PM
 *
 * @author Jack
 */

namespace duels;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\entity\ProjectileHitEvent;

use pocketmine\block\Block;

class EventListener implements Listener {

	/** @var Main */
	private $plugin;

	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
		$plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
	}

	/**
	 * @return Main
	 */
	public function getPlugin() {
		return $this->plugin;
	}

	/**
	 * @param PlayerCreationEvent $event
	 *
	 * @priority HIGHEST
	 */
	public function onCreation(PlayerCreationEvent $event) {
		$event->setPlayerClass(DuelsPlayer::class);
	}

	public function onJoin(PlayerJoinEvent $event) {
		$player = $event->getPlayer();
	}

	/**
	 * @param PlayerChatEvent $event
	 *
	 * @priority HIGHEST
	 */
	public function onChat(PlayerChatEvent $event) {
		/** @var DuelsPlayer $player */
		$player = $event->getPlayer();

	}

	public function onExplode(EntityExplodeEvent $event) {
		$event->setBlockList([]);
	}

	public function onHit(ProjectileHitEvent $event) {
		$event->getEntity()->kill();
	}

	public function close() {
		unset($this->plugin);
	}

}