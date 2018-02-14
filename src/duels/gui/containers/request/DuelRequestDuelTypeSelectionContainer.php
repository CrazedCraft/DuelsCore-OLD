<?php

declare(strict_types=1);

namespace duels\gui\containers\request;

use core\language\LanguageUtils;
use duels\gui\containers\generic\DuelTypeSelectionContainer;
use duels\gui\item\request\DuelRequestDuelTypeSelectionItem;
use duels\Main;

class DuelRequestDuelTypeSelectionContainer extends DuelTypeSelectionContainer {

	const CONTAINER_ID = "DUEL_REQUEST_DUEL_TYPE_SELECTION_CONTAINER";

	public function __construct(Main $plugin) {
		parent::__construct($plugin, LanguageUtils::translateColors("&l&eSelect a duel type"), DuelRequestDuelTypeSelectionItem::class);
	}

}