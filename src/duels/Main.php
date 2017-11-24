<?php

/**
 * Duels Main class
 *
 * Created on 31/03/2016 at 9:33 PM
 *
 * @author Jack
 */

namespace duels;

use core\util\traits\CorePluginReference;
use duels\duel\DuelManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\PluginException;

class Main extends PluginBase {

	use CorePluginReference;

	/** @var EventListener */
	private $listener;

	/** @var DuelManager */
	private $duelManager;

	public function onEnable() {
		$components = $this->getServer()->getPluginManager()->getPlugin("Components");
		if(!$components instanceof \core\Main) {
			throw new PluginException("Components plugin isn't loaded!");
		}
		$this->setCore($components);

		$this->setListener();
		$this->setDuelManager();
	}

	/**
	 * Safely closes everything and dumps all data
	 */
	public function onDisable() {
		$this->listener->close();
		$this->duelManager->close();
	}

	protected function registerCommands() {
		$this->getCore()->getCommandMap()->registerAll([

		]);
	}

	/**
	 * @return EventListener
	 */
	public function getListener() {
		return $this->listener;
	}

	/**
	 * @return DuelManager
	 */
	public function getDuelManager() {
		return $this->duelManager;
	}

	/**
	 * Set's the listener
	 */
	public function setListener() {
		$this->listener = new EventListener($this);
	}

	/**
	 * Set's the duel manager
	 */
	public function setDuelManager() {
		$this->duelManager = new DuelManager($this);
	}

}