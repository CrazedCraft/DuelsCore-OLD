<?php

declare(strict_types=1);

namespace duels\ui\windows\generic;

use duels\Main;
use duels\ui\elements\generic\DuelTypeSelectionButton;
use pocketmine\customUI\windows\SimpleForm;

abstract class DuelTypeSelectionForm extends SimpleForm {

	/** @var string */
	private $buttonClass;

	public function __construct(string $title, string $buttonClass = DuelTypeSelectionButton::class) {
		$this->buttonClass = $buttonClass;
		parent::__construct($title, "");

		$this->setDefaultTypes();
	}

	public function setDefaultTypes() {
		foreach(Main::getInstance()->getDuelManager()->getDuelTypes() as $type) {
			$this->addButton(new $this->buttonClass($type));
		}
	}

}