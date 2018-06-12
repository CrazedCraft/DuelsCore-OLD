<?php

namespace duels\duel;

use core\language\LanguageUtils;
use duels\arena\Arena;
use duels\DuelsPlayer;
use duels\kit\Kit;
use duels\Main;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\tile\Skull;
use pocketmine\utils\TextFormat as TF;

class DuelManager {

	/** @var Duel[] */
	public $duels = [];

	/** @var DuelType[] */
	private $types = [];

	/** @var Main */
	private $plugin;

	public function __construct(Main $plugin) {
		$this->plugin = $plugin;

		$this->loadTypes();
	}

	/**
	 * Load the default duel types
	 */
	private function loadTypes() : void {
		$this->types[DuelType::DUEL_TYPE_1V1] = new DuelType($this, DuelType::DUEL_TYPE_1V1, LanguageUtils::translateColors("&l&31v1"), Item::get(Item::MOB_HEAD, Skull::TYPE_HUMAN, 1), "http://jacknoordhuis.net/minecraft/icons/items/397-3.png", 2, 2);
		$this->types[DuelType::DUEL_TYPE_2v2] = new DuelType($this, DuelType::DUEL_TYPE_2v2, LanguageUtils::translateColors("&l&32v2"), Item::get(Item::MOB_HEAD, Skull::TYPE_HUMAN, 2), "http://jacknoordhuis.net/minecraft/icons/items/397-3.png", 4, 4);
		$this->types[DuelType::DUEL_TYPE_FFA] = new DuelType($this, DuelType::DUEL_TYPE_FFA, LanguageUtils::translateColors("&l&3FFA"), Item::get(Item::MOB_HEAD, Skull::TYPE_DRAGON, 1), "http://jacknoordhuis.net/minecraft/icons/items/397-5.png", 24, 2);
	}

	/**
	 * Check if a duel type exists
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public function duelTypeExists(int $id) : bool {
		return isset($this->types[$id]) and $this->types[$id] instanceof DuelType;
	}

	/**
	 * Get a duel type
	 *
	 * @param int $id
	 *
	 * @return DuelType|null
	 */
	public function getDuelType(int $id) : ?DuelType {
		if($this->duelTypeExists($id)) {
			return $this->types[$id];
		}

		return null;
	}

	/**
	 * @return DuelType[]
	 */
	public function getDuelTypes() : array {
		return $this->types;
	}

	public function getAll() {
		return $this->duels;
	}

	public function isDuel(int $id) {
		return isset($this->duels[$id]) and $this->duels[$id] instanceof Duel;
	}

	public function removeDuel($id) {
		if(!$this->getDuel($id)) return;
		unset($this->duels[$id]);
	}

	public function getDuel(int $id) : ?Duel {
		if($this->isDuel($id)) {
			return $this->duels[$id];
		}

		return null;
	}

	public function findDuel(DuelsPlayer $player, int $type, Kit $kit = null, $checkOs = false) {
		$type = $this->getDuelType($type);

		if($type === null) {
			$player->sendTip(TF::GOLD . "Uh oh, looks like something went wrong! Try again!");
			return null;
		}

		foreach($this->duels as $duel) {
			if($duel->isJoinable()) {
				if($duel->getType()->getId() === $type->getId()) {
					if($checkOs and !$duel->matchesOs($player->getDeviceOS())) continue;
					if($kit instanceof Kit and $kit->getName() != $duel->getKit()->getName()) continue;
					$duel->addPlayer($player);
					return;
				} else {
					continue;
				}
			}
		}
		if(count($this->duels) <= 30) {
			$this->addDuel($type, $kit, $player->getDeviceOS());
			foreach($this->duels as $duel) {
				if($duel->isJoinable()) {
					if($duel->getType()->getId() === $type->getId()) {
						if($checkOs and !$duel->matchesOs($player->getDeviceOS())) continue;
						if($kit instanceof Kit and $kit->getName() != $duel->getKit()->getName()) continue;
						$duel->addPlayer($player);
						return;
					}
				}
			}
		}
		$player->sendTip(TF::GOLD . "Looks like all duels are full, try again in a moment!");
	}

	public function addDuel($type, Kit $kit = null, $deviceOs = 1) {
		//if(count($this->duels) >= 32) return;
		/** @var Arena $arena */
		$arena = $this->plugin->getArenaManager()->find();
		if((!$arena instanceof Arena) or isset($this->duels[$arena->getId()]) or $arena->inUse) return;
		$arena->inUse = true;
		$this->plugin->getArenaManager()->remove($arena->getId());
		$this->duels[$arena->getId()] = $duel = new Duel($this->plugin, $type, $arena, ($kit instanceof Kit ? $kit : $this->plugin->getKitManager()->getRandomKit()));
		$duel->setOs($duel->matchesOS($deviceOs) ? Duel::OS_MOBILE : Duel::OS_WINDOWS);
	}

	public function close() {
		foreach($this->duels as $d) {
			$d->end();
		}
		unset($this->duels);
		//unset($this->plugin);
	}

}