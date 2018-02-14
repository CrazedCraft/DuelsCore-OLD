<?php

declare(strict_types=1);

namespace duels\duel;

use core\language\LanguageUtils;
use pocketmine\item\Item;

class DuelType {

	/** @var DuelManager */
	private $duelManager;

	/** @var int */
	private $id;

	/** @var string */
	private $name;

	/** @var string */
	private $display;

	/** @var Item */
	private $displayItem;

	/** @var string */
	private $displayImage;

	/** @var int */
	private $maxPlayers;

	/** @var int */
	private $minPlayers;

	const DUEL_TYPE_1V1 = 0;
	const DUEL_TYPE_2v2 = 1;
	const DUEL_TYPE_FFA = 2;

	public function __construct(DuelManager $manager, int $id, string $name, Item $displayItem, string $displayImage, int $maxPlayers, int $minPlayers) {
		$this->duelManager = $manager;
		$this->id = $id;
		$this->name = LanguageUtils::cleanString($name);
		$this->display = $name;
		$displayItem->setCustomName($name);
		$this->displayItem = $displayItem;
		$this->displayImage = $displayImage;
		$this->maxPlayers = $maxPlayers;
		$this->minPlayers = $minPlayers;
	}

	/**
	 * @return DuelManager
	 */
	public function getDuelManager() : DuelManager {
		return $this->duelManager;
	}

	/**
	 * @return int
	 */
	public function getId() : int {
		return $this->id;
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
	public function getDisplay() : string {
		return $this->display;
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
	public function getDisplayImage() : string {
		return $this->displayImage;
	}

	/**
	 * @return int
	 */
	public function getMaxPlayers() : int {
		return $this->maxPlayers;
	}

	/**
	 * @return int
	 */
	public function getMinPlayers() : int {
		return $this->minPlayers;
	}

}