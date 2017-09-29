<?php
/**
 * DuelsCore – DefaultServerSelectionForm.php
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
 * Created on 13/9/17 at 7:03 PM
 *
 */

namespace duels\ui\windows;

use duels\ui\elements\server\selectors\ClassicPrisonServerSelectionButton;
use duels\ui\elements\server\selectors\ClassicPvPServerSelectionButton;
use duels\ui\elements\server\selectors\DuelsServerSelectionButton;
use duels\ui\windows\generic\ServerSelectionForm;

class DefaultServerSelectionForm extends ServerSelectionForm {

	const FORM_UI_ID = "DEFAULT_SERVER_SELECTION_FORM";

	public function __construct() {
		parent::__construct("&l&dServer Selector");
	}

	public function addDefaultButtons() {
		$this->addButton(new DuelsServerSelectionButton(1)); // Duel-1
		$this->addButton(new DuelsServerSelectionButton(2)); // Duel-2

		$this->addButton(new ClassicPvPServerSelectionButton()); // Classic PvP

		$this->addButton(new ClassicPrisonServerSelectionButton()); // Classic prison
	}

}