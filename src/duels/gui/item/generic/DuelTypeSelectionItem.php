<?php

declare(strict_types=1);

namespace duels\gui\item\generic;

use core\gui\container\ContainerGUI;
use core\gui\item\GUIItem;
use duels\duel\DuelType;
use duels\Main;

abstract class DuelTypeSelectionItem extends GUIItem {

	/** @var int */
	private $typeId;

	public function __construct(DuelType $type, ?ContainerGUI $parent = null) {
		$this->typeId = $type->getId();
		parent::__construct($type->getDisplayItem(), $parent);
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