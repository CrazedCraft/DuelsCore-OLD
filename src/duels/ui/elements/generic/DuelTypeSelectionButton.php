<?php

declare(strict_types=1);

namespace duels\ui\elements\generic;

use duels\duel\DuelType;
use duels\Main;
use pocketmine\customUI\elements\simpleForm\Button;

abstract class DuelTypeSelectionButton extends Button {

	/** @var int */
	private $typeId;

	public function __construct(DuelType $type) {
		$this->typeId = $type->getId();
		parent::__construct($type->getDisplay());
		$this->addImage(Button::IMAGE_TYPE_URL, $type->getDisplayImage());
	}

	/**
	 * @return int
	 */
	public function getDuelTypeId() : int {
		return $this->typeId;
	}

	/**
	 * @return DuelType|null
	 */
	public function getDuelType() : ? DuelType {
		return Main::getInstance()->getDuelManager()->getDuelType($this->typeId);
	}

}