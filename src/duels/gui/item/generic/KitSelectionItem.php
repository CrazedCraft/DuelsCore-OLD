<?php

declare(strict_types=1);

namespace duels\gui\item\generic;

use core\gui\container\ContainerGUI;
use core\gui\item\GUIItem;
use duels\kit\Kit;
use duels\Main;

abstract class KitSelectionItem extends GUIItem {

	/** @var string */
	private $kitName = "";

	public function __construct(Kit $kit, ?ContainerGUI $parent = null) {
		$this->kitName = $kit->getName();
		parent::__construct($kit->getDisplayItem(), $parent);
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
		return Main::getInstance()->getKitManager()->getKit($this->kitName);
	}

}