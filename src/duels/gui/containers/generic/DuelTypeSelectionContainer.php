<?php

declare(strict_types=1);

namespace duels\gui\containers\generic;

use core\gui\container\ChestGUI;
use duels\Main;

abstract class DuelTypeSelectionContainer extends ChestGUI {

	/** @var string */
	private $itemClass;

	public function __construct(Main $plugin, string $title, string $itemClass) {
		$this->itemClass = $itemClass;
		parent::__construct($plugin->getCore());

		$this->setDefaultTypes();
	}

	public function setDefaultTypes() {
		$i = 0;
		foreach(Main::getInstance()->getDuelManager()->getDuelTypes() as $type) {
			$this->setItem($i++, new $this->itemClass($type, $this));
		}
	}

}