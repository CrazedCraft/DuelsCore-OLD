<?php

namespace duels\kit;

use core\Utils as CUtils;
use pocketmine\item\Item;
use pocketmine\nbt\tag\StringTag;
use pocketmine\utils\TextFormat;

class Kit {

	/** @var string */
	private $name = "";

	/** @var Item */
	protected $displayItem;

	/** @var string */
	private $type = "";

	/** @var string */
	private $description = "";

	/** @var Item[] */
	private $items = [];

	/** @var Item[] */
	private $armor = [];

	/** @var string */
	private $imageFile = "";

	public function __construct(string $name, Item $displayItem, string $type, string $description, array $items, array $armor, string $imageFile = "0-0.png") {
		$this->name = $name;
		$displayItem->setCustomName($name . " Kit");
		$tag = $displayItem->getNamedTag();
		$tag->KitName = new StringTag("KitName", $this->getName());
		$displayItem->setNamedTag($tag);
		$this->displayItem = $displayItem;
		$this->type = $type;
		$this->description = $description;
		$this->items = $items;
		$this->armor = $armor;
		$this->imageFile = $imageFile;
	}

	public function getName() {
		return CUtils::cleanString($this->name);
	}

	public function getDisplayName() {
		return $this->name;
	}

	public function getDisplayItem() {
		return $this->displayItem;
	}

	public function getType() {
		return $this->type;
	}

	public function getDescription() {
		return $this->description;
	}

	public function getItems() {
		return $this->items;
	}

	public function getArmor() {
		return $this->armor;
	}

	public function getImageFile() {
		return $this->imageFile;
	}

}
