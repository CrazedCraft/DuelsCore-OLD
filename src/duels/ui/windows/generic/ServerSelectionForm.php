<?php

/**
 * DuelsCore – ServerSelectionForm.php
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
 * Created on 13/9/17 at 7:00 PM
 *
 */

namespace duels\ui\windows\generic;

use pocketmine\customUI\windows\SimpleForm;

abstract class ServerSelectionForm extends SimpleForm {

	public function __construct(string $title) {
		parent::__construct($title, "");

		$this->addDefaultButtons();
	}

	abstract public function addDefaultButtons();

}