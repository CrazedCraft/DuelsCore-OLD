<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 20/4/17
 * Time: 9:07 PM
 */

namespace duels\kit;

use pocketmine\item\Item;

class RandomKit extends Kit {

	public function __construct($name, Item $displayItem, string $type, string $description, array $items, array $armor, string $imageFile = "0-0.png") {
		parent::__construct($name, $displayItem, $type, $description, $items, $armor, $imageFile);
		$this->displayItem->setCustomName($name);
	}

}