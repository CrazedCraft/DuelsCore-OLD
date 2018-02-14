<?php

declare(strict_types=1);

namespace duels\ui\windows\play;

use core\language\LanguageUtils;
use duels\ui\elements\play\PlayDuelTypeSelectionButton;
use duels\ui\windows\generic\DuelTypeSelectionForm;

class PlayDuelTypeSelectionForm extends DuelTypeSelectionForm {

	const FORM_UI_ID = "PLAY_DUEL_TYPE_SELECTION_FORM";

	public function __construct() {
		parent::__construct(LanguageUtils::translateColors("&l&eSelect a duel type to play"), PlayDuelTypeSelectionButton::class);
	}

}