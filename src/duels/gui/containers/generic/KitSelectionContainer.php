<?php

declare(strict_types=1);

namespace duels\gui\containers\generic;

use core\gui\container\ChestGUI;
use duels\Main;

abstract class KitSelectionContainer extends ChestGUI {

	/** @var string */
	private $itemClass;

	public function __construct(Main $plugin, string $title, string $itemClass) {
		$this->itemClass = $itemClass;
		parent::__construct($plugin->getCore());

		$this->setDefaultKits();
	}

	public function setDefaultKits() {
		$i = 0;
		foreach(Main::getInstance()->getKitManager()->getKits() as $kit) {
			$this->setItem($i++, new $this->itemClass($kit, $this));
		}
	}

}