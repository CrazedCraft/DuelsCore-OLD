<?php

declare(strict_types=1);

namespace duels\gui\containers\play;

use core\language\LanguageUtils;
use duels\gui\containers\generic\KitSelectionContainer;
use duels\gui\item\play\PlayKitSelectionItem;
use duels\Main;

class PlayKitSelectionContainer extends KitSelectionContainer {

	const CONTAINER_ID = "PLAY_KIT_SELECTION_CONTAINER";

	public function __construct(Main $plugin) {
		parent::__construct($plugin, LanguageUtils::translateColors("&l&eSelect a kit to play"), PlayKitSelectionItem::class);
	}

}