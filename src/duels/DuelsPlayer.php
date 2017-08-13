<?php

/**
 * Duels_v1-Alpha – DuelsPlayer.php
 *
 * Copyright (C) 2017 Jack Noordhuis
 *
 * This is private software, you cannot redistribute and/or modify it in any way
 * unless given explicit permission to do so. If you have not been given explicit
 * permission to view or modify this software you should take the appropriate actions
 * to remove this software from your device immediately.
 *
 * @author Jack Noordhuis
 *
 * Created on 7/8/17 at 3:18 PM
 *
 */

namespace duels;

use core\CorePlayer;

class DuelsPlayer extends CorePlayer {

	/** @var string */
	private $lastTappedPLayerUuid = "";

	/** @var int */
	private $lastSelectedKitId = -1;

	/** @var bool */
	private $requestStatus = true;

	/** @var array */
	private $requestIds = [];

	/** @var int */
	private $duelId = -1;

	/** @var int */
	private $lasSelectedPartyType = -1;

}