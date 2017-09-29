<?php

/**
 * DuelsCore – KitSelectionForm.php
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
 * Created on 12/9/17 at 5:53 PM
 *
 */

namespace duels\ui\windows\generic;

use duels\Main;
use duels\ui\elements\generic\KitSelectionButton;
use pocketmine\customUI\windows\SimpleForm;

abstract class KitSelectionForm extends SimpleForm {

	/** @var string */
	private $buttonClass;

	public function __construct(string $title, string $buttonClass = KitSelectionButton::class) {
		$this->buttonClass = $buttonClass;
		parent::__construct($title, "");

		$this->setDefaultKits();
	}

	public function setDefaultKits() {
		foreach(Main::getInstance()->getKitManager()->getAll() as $kit) {
			$this->addButton(new $this->buttonClass($kit));
		}
	}

}