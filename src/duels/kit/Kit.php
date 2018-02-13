<?php

namespace duels\kit;

use core\exception\InvalidConfigException;
use core\language\LanguageUtils;
use core\util\ConfigUtils;
use duels\DuelsPlayer;
use pocketmine\item\Item;

class Kit {

	/**
	 * Load a kit from data
	 *
	 * @param KitManager
	 * @param array $data
	 *
	 * @return Kit
	 */
	public static function fromData(KitManager $manager, array $data) : Kit {
		return new Kit($manager, LanguageUtils::translateColors($data["name"]), ConfigUtils::parseArrayItem($data["display_item"]), $data["type"], ConfigUtils::parseArrayItems($data["items"] ?? []), ConfigUtils::parseArrayItems($data["armor"] ?? []), $data["image"]);
	}

	/** @var KitManager */
	private $manager;

	/** @var string */
	private $name;

	/** @var string */
	private $displayName;

	/** @var Item */
	private $displayItem;

	/** @var string */
	private $type = "";

	/** @var Item[] */
	private $items = [];

	/** @var Item[] */
	private $armor = [];

	/** @var string */
	private $imageFile = "";

	const TYPE_RANDOM = "random";
	const TYPE_KIT = "kit";

	public function __construct(KitManager $manager, string $name, Item $displayItem, string $type, array $items, array $armor, string $imageFile = "0-0.png") {
		$this->manager = $manager;

		$this->name = LanguageUtils::cleanString($name);
		$this->displayName = $name;

		$displayItem->setCustomName($name . " Kit");
		$this->displayItem = $displayItem;

		$this->type = $type;

		$this->items = $items;
		$this->armor = $armor;

		$this->imageFile = $imageFile;
	}

	/**
	 * @return KitManager
	 */
	public function getManager() : KitManager {
		return $this->manager;
	}

	/**
	 * @return string
	 */
	public function getName() : string {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getDisplayName() : string {
		return $this->displayName;
	}

	/**
	 * @return Item
	 */
	public function getDisplayItem() : Item {
		return $this->displayItem;
	}

	/**
	 * @return string
	 */
	public function getType() : string {
		return $this->type;
	}

	/**
	 * @return Item[]
	 */
	public function getItems() : array {
		return $this->items;
	}

	/**
	 * @return Item[]
	 */
	public function getArmor() : array {
		return $this->armor;
	}

	/**
	 * @return string
	 */
	public function getImageFile() : string {
		return $this->imageFile;
	}

	/**
	 * Apply this kit to a player
	 *
	 * @param DuelsPlayer $player
	 */
	public function applyTo(DuelsPlayer $player) : void {
		if($this->type === self::TYPE_KIT) {
			$inv = $player->getInventory();
			foreach($this->items as $i => $item) {
				$inv->setItem($i, $item);
			}
			$inv->setArmorContents($this->armor);
		}
	}

}