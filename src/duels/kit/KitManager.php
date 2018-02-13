<?php

namespace duels\kit;

use core\exception\InvalidConfigException;
use core\Utils;
use duels\Main;

/**
 * Class that manages the kits for duels
 */
class KitManager {

	/** @var Main */
	private $plugin;

	/** @var Kit[] */
	private $kits = [];

	/* Path to where the kit data is stored */
	const KITS_DATA_FILE = "data" . DIRECTORY_SEPARATOR . "kits.json";

	public function __construct(Main $plugin) {
		$this->plugin = $plugin;

		$this->registerFromData();
	}

	public function getPlugin() : Main {
		return $this->plugin;
	}

	/**
	 * Registers the kit data from kits.json
	 */
	private function registerFromData() : void {
		$this->plugin->saveResource(self::KITS_DATA_FILE);
		$data = Utils::getJsonContents($this->plugin->getDataFolder() . self::KITS_DATA_FILE);
		foreach($data as $i => $kitData) {
			try {$this->addKit(Kit::fromData($this, $kitData));

			} catch(InvalidConfigException $e) {
				$this->plugin->getLogger()->warning("Could not load kit #{$i} due to invalid config! Message: {$e->getMessage()}");
			}
		}
	}

	/**
	 * @param Kit $kit
	 */
	public function addKit(Kit $kit) : void{
		$this->kits[strtolower($kit->getName())] = $kit;
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function kitExists(string $name) : bool {
		return isset($this->kits[strtolower($name)]) and $this->kits[$name] instanceof Kit;
	}

	/**
	 * @param string $name
	 *
	 * @return Kit|null
	 */
	public function getKit(string $name) : ?Kit {
		$name = strtolower($name);
		if($this->kitExists($name)) {
			return $this->kits[$name];
		}

		return null;
	}

	/**
	 * @return Kit
	 */
	public function getRandomKit() : Kit {
		$kit = $this->kits[array_rand($this->kits)];
		return $kit->getType() === Kit::TYPE_KIT ? $kit : $this->getRandomKit();
	}

	/**
	 * @return Kit[]
	 */
	public function getKits() : array {
		return $this->kits;
	}

}
