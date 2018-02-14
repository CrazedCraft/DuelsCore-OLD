<?php

/**
 * DuelsCore – PlayKitSelectionForm.php
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
 * Created on 12/9/17 at 7:35 PM
 *
 */

namespace duels\ui\windows\play;

use core\language\LanguageUtils;
use duels\ui\elements\play\PlayKitSelectionButton;
use duels\ui\windows\generic\KitSelectionForm;

class PlayKitSelectionForm extends KitSelectionForm {

	const FORM_UI_ID = "PLAY_KIT_SELECTION_FORM";

	public function __construct() {
		parent::__construct(LanguageUtils::translateColors("&l&eSelect a kit to play"), PlayKitSelectionButton::class);
	}

}