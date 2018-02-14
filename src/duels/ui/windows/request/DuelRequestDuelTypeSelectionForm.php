<?php

declare(strict_types=1);

namespace duels\ui\windows\request;

use core\language\LanguageUtils;
use duels\ui\elements\request\DuelRequestDuelTypeSelectionButton;
use duels\ui\windows\generic\DuelTypeSelectionForm;

class DuelRequestDuelTypeSelectionForm extends DuelTypeSelectionForm {

	const FORM_UI_ID = "DUEL_REQUEST_DUEL_TYPE_SELECTION_FORM";

	public function __construct() {
		parent::__construct(LanguageUtils::translateColors("&l&eSelect a duel type"), DuelRequestDuelTypeSelectionButton::class);
	}

}