<?php

/**
 * DuelsPlayer.php class
 *
 * Created on 12/04/2016 at 11:01 AM
 *
 * @author Jack
 */

namespace duels;

use core\CorePlayer;
use duels\duel\Duel;
use duels\party\Party;
use duels\session\DuelRequest;

class DuelsPlayer extends CorePlayer {

	/** @var bool */
	public $requestsEnabled = true;

	/** @var array */
	public $requests = [];

}