<?php

/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 20/4/17
 * Time: 6:39 PM
 */

namespace duels\gui\item\serverselectors;

use core\gui\item\GUIItem;
use core\language\LanguageUtils;
use duels\gui\containers\ServerSelectionContainer;
use pocketmine\item\Item;

class DuelsServerSelector extends GUIItem {

	const GUI_ITEM_ID = "duels_server_selector";

	public function __construct(ServerSelectionContainer $parent = null) {
		parent::__construct(Item::get(Item::DIAMOND_SWORD, 0, 1), $parent);
		$this->setCustomName(LanguageUtils::translateColors("&l&bDuels"));
		$this->giveEnchantmentEffect();
	}

	public function getCooldown() : int {
		return 0; // in seconds
	}

}