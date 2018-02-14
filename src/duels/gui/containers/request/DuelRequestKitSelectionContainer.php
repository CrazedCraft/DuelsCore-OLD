<?php

declare(strict_types=1);

namespace duels\gui\containers\request;

use core\language\LanguageUtils;
use duels\gui\containers\generic\KitSelectionContainer;
use duels\gui\item\request\DuelRequestKitSelectionItem;
use duels\Main;

class DuelRequestKitSelectionContainer extends KitSelectionContainer {

	const CONTAINER_ID = "DUEL_REQUEST_KIT_SELECTION_CONTAINER";

	public function __construct(Main $plugin) {
		parent::__construct($plugin, LanguageUtils::translateColors("&l&eSelect a kit"), DuelRequestKitSelectionItem::class);
	}

}