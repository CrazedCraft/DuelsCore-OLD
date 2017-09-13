<?php

/**
 * DuelsCore – KitSelectionButtonButton.php
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
 * Created on 12/9/17 at 5:56 PM
 *
 */

namespace duels\ui\elements\generic;

use duels\kit\Kit;
use duels\Main;
use pocketmine\customUI\elements\simpleForm\Button;

abstract class KitSelectionButton extends Button {

	/** @var string */
	private $kitName = "";

	public function __construct(Kit $kit) {
		$this->kitName = $kit->getName();
		parent::__construct($kit->getDisplayName() . " Kit");
		$this->addImage(Button::IMAGE_TYPE_URL, "http://jacknoordhuis.net/minecraft/icons/items/{$kit->getImageFile()}");
	}

	/**
	 * @return string
	 */
	public function getKitName() : string {
		return $this->kitName;
	}

	/**
	 * @return \duels\kit\Kit|null
	 */
	public function getKit() {
		return Main::getInstance()->getKitManager()->get($this->kitName);
	}

}