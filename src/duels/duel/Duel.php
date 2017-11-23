<?php

/**
 * Duel.php class
 *
 * Created on 29/04/2016 at 6:59 PM
 *
 * @author Jack
 */

namespace duels\duel;

use core\CorePlayer;
use core\game\Match;
use core\language\LanguageManager;
use duels\DuelsPlayer;

class Duel extends Match {

	/** @var DuelManager */
	private $manager;

	public function __construct(DuelManager $manager) {
		$this->manager = $manager;
	}

	/**
	 * @return DuelManager
	 */
	public function getManager() {
		return $this->manager;
	}


}