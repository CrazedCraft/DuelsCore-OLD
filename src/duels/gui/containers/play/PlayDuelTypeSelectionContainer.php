<?php

declare(strict_types=1);

namespace duels\gui\containers\play;

use core\language\LanguageUtils;
use duels\gui\containers\generic\DuelTypeSelectionContainer;
use duels\gui\item\play\PlayDuelTypeSelectionItem;
use duels\Main;

class PlayDuelTypeSelectionContainer extends DuelTypeSelectionContainer {

	const CONTAINER_ID = "PLAY_DUEL_TYPE_SELECTION_CONTAINER";

	public function __construct(Main $plugin) {
		parent::__construct($plugin, LanguageUtils::translateColors("&l&eSelect a duel type to play"), PlayDuelTypeSelectionItem::class);
	}

}