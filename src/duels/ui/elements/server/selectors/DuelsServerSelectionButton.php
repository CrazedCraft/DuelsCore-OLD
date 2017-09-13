<?php

/**
 * DuelsCore – DuelsServerSelectionButton.php
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
 * Created on 13/9/17 at 6:56 PM
 *
 */

namespace duels\ui\elements\server\selectors;

use core\network\NetworkNode;
use duels\ui\elements\generic\ServerSelectionButton;

/**
 * TODO: Remove this and replace with automatically generated list (probably through a database to make updating all servers using this transfer form easier?) of available servers
 */
class DuelsServerSelectionButton extends ServerSelectionButton {

	public function __construct(int $serverId) {
		parent::__construct("&l&bDuel {$serverId}", NetworkNode::NODE_DUEL, $serverId, "276-0.png");
	}

}