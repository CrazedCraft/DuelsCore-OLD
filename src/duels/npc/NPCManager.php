<?php

namespace duels\npc;

use duels\duel\Duel;
use duels\entity\HumanNPC;
use duels\Main;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\Enum;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\utils\TextFormat as TF;

class NPCManager {

	private $plugin;

	private $data = [];

	/** @var HumanNPC[] */
	public $NPCs = [];

	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
		Entity::registerEntity(HumanNPC::class, true);
		$this->data = ["0" => ["name" => TF::YELLOW . "» Play - 1v1 «", "pos" => ["x" => 0.5, "y" => 96, "z" => 69.5], "rotation" => ["yaw" => 180, "pitch" => 0,], "skin" => "default", "type" => Duel::TYPE_1V1], "1" => ["name" => TF::YELLOW . "» Play - 2v2 «", "pos" => ["x" => 69.5, "y" => 96, "z" => 0.5], "rotation" => ["yaw" => 90, "pitch" => 0,], "skin" => "default", "type" => Duel::TYPE_2V2]];
		$this->spawn();
	}

	public function spawn() {
		$level = $this->plugin->getServer()->getDefaultLevel();
		foreach($this->createNBT() as $key => $nbt) {
			if(!$level->isChunkLoaded($this->data[$key]["pos"]["x"] >> 4, $this->data[$key]["pos"]["z"] >> 4)) $level->loadChunk($this->data[$key]["pos"]["x"] >> 4, $this->data[$key]["pos"]["z"] >> 4, true);
			$chunk = $this->plugin->getServer()->getDefaultLevel()->getChunk($this->data[$key]["pos"]["x"] >> 4, $this->data[$key]["pos"]["z"] >> 4);
			$entity = Entity::createEntity("HumanNPC", $chunk, $nbt);
			if(!$entity instanceof HumanNPC) continue;
			$this->NPCs[] = $entity;
			$chunk->allowUnload = false;
//			$skin = Skin::get($this->plugin->getDataFolder() . "skins" . DIRECTORY_SEPARATOR, "default");
			$entity->setCustomName($nbt["CustomName"]);
			$entity->showPlaying(0);
//			$entity->setSkin($skin["skin"], "Standard_Custom");
			$entity->spawnToAll();
		}
	}

	public function createNBT() {
		$nbt = [];
		foreach($this->data as $key => $data) {
			if(is_file($this->plugin->getDataFolder() . "npcs" . DIRECTORY_SEPARATOR . TF::clean($data["name"]) . ".npc")) continue;
			$nbt[$key] = new Compound("", [
				new Enum("Pos", [
					new DoubleTag("", $data["pos"]["x"]),
					new DoubleTag("", $data["pos"]["y"]),
					new DoubleTag("", $data["pos"]["z"])
				]),

				new Enum("Motion", [
					new DoubleTag("", 0),
					new DoubleTag("", 0), new DoubleTag("", 0)
				]),

				new Enum("Rotation", [
					new FloatTag("", $data["rotation"]["yaw"]),
					new FloatTag("", $data["rotation"]["pitch"])
				]),

				new StringTag("CustomName", $data["name"]),

				new ShortTag("Health", 20),

				new StringTag("Type", $data["type"])

			]);
		}
		return $nbt;
	}

	/**
	 * Stop loaded chunks from being unloaded
	 */
	public function freezeLoadedChunks() {
		$chunks = $this->plugin->getServer()->getDefaultLevel()->getProvider()->getLoadedChunks();
		foreach($chunks as $chunk) {
			$chunk->allowUnload = false;
		}
	}

	public static function add($dir, $name) {
		return;
	}

	public static function exists($dir, $name) {
		return;
	}

	public static function remove($dir, $name) {
		return;
	}

}