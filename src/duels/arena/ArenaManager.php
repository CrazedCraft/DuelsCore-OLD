<?php

namespace duels\arena;

use core\Utils;
use duels\Main;

class ArenaManager {

	private $plugin;

	/** @var Arena[] */
	private $arenas = [];

	/** @var int */
	public static $arenaCount = 0;

	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
		$this->loadArenas();
		var_dump(count($this->arenas));
	}

	public function loadArenas() {
		$this->plugin->saveResource("arenas.json");
		foreach(json_decode(file_get_contents($this->plugin->getDataFolder() . "arenas.json"), true) as $arena) {
			$this->add($arena["author"], $arena["spawn-positions"]);
		}
	}

	public function add(string $creator, array $locations) {
		$id = ArenaManager::$arenaCount++;
		$this->arenas[$id] = new Arena($id, $creator, $this->parseVectors($locations));
	}

	public function addBack(Arena $arena) {
		$arena->inUse = false;
		$this->arenas[$arena->getId()] = $arena;
	}

	public function get($id) {
		if(!$this->exists($id)) return null;
		return $this->arenas[$id];
	}

	public function exists($id) {
		return isset($this->arenas[$id]);
	}

	public function remove($id) {
		if(!$this->exists($id)) return false;
		$this->arenas[$id]->inUse = true;
		unset($this->arenas[$id]);
		return true;
	}

	public function find() {
		return empty($this->arenas) ? null : $this->arenas[array_rand($this->arenas)];
	}

	public function parseVectors(array $positions) {
		$array = [];
		foreach($positions as $pos) {
			$array[] = Utils::parseVector($pos);
		}
		return $array;
	}

}