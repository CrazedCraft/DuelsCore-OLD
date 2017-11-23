<?php

/**
 * DuelManager.php class
 *
 * Created on 3/05/2016 at 2:48 PM
 *
 * @author Jack
 */

namespace duels\duel;


use core\game\MatchManager;
use duels\Main;

class DuelManager extends MatchManager {

	/** @var Main */
	private $plugin;

	/** @var Duel[] */
	private $duels = [];

	public function __construct(Main $plugin) {
		parent::__construct($plugin->getCore());
		$this->plugin = $plugin;
	}

	/**
	 * @return Main
	 */
	public function getPlugin() {
		return $this->plugin;
	}

	/**
	 * Safely close all duels and dump all data
	 */
	public function close() {
		foreach($this->duels as $duel) {
			$duel->close();
		}
		unset($this->plugin);
	}

}