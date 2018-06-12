<?php

namespace duels;

use core\entity\BossBar;
use core\entity\text\UpdatableFloatingText;
use core\gui\item\defaults\serverselector\ServerSelector;
use core\gui\item\defaults\SpawnWarpItem;
use core\util\traits\CorePluginReference;
use duels\duel\DuelType;
use duels\entity\SelectionNPC;
use duels\gui\containers\play\PlayDuelTypeSelectionContainer;
use duels\gui\containers\play\PlayKitSelectionContainer;
use duels\gui\containers\request\DuelRequestDuelTypeSelectionContainer;
use duels\gui\containers\request\DuelRequestKitSelectionContainer;
use duels\gui\item\duel\DuelKitRequestSelector;
use duels\gui\item\party\PartyEventSelector;
use core\language\LanguageUtils;
use duels\arena\ArenaManager;
use duels\command\DuelCommand;
use duels\command\HubCommand;
use duels\command\PartyCommand;
use duels\duel\Duel;
use duels\duel\DuelManager;
use duels\gui\item\kit\KitSelector;
use duels\kit\KitManager;
use duels\npc\NPCManager;
use duels\party\PartyManager;
use duels\tasks\UpdateInfoTextTask;
use duels\ui\windows\play\PlayDuelTypeSelectionForm;
use duels\ui\windows\play\PlayKitSelectionForm;
use duels\ui\windows\request\DuelRequestDuelTypeSelectionForm;
use duels\ui\windows\request\DuelRequestKitSelectionForm;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\plugin\PluginException;
use pocketmine\utils\TextFormat;

class Main extends PluginBase {

	use CorePluginReference;

	const GUI_PARTY_TYPE_SELECTION_CONTAINER = "party_type_selection_container";
	const GUI_PARTY_KIT_SELECTION_CONTAINER = "party_kit_selection_container";

	/** @var Main */
	public static $instance = null;

	/** @var EventListener */
	public $listener;

	/** @var ArenaManager */
	public $arenaManager;

	/** @var NPCManager */
	public $npcManager;

	/** @var DuelManager */
	public $duelManager;

	/** @var KitManager */
	public $kitManager;

	/** @var PartyManager */
	public $partyManager;
	/** @var int */
	public $toRestart = 60 * 60;

	/** @var UpdatableFloatingText[] */
	public $infoText = [];

	/** @var Vector3 */
	public static $spawnCoords = null;

	/** @var BossBar */
	public $lobbyBossBar = null;

	/** @var SelectionNPC */
	private $npcs = [];

	/** @var array */
	public static $languages = [
		"en" => "english.json"
	];

	const MESSAGES_FILE_PATH = "messages" . DIRECTORY_SEPARATOR;

	public static function getInstance() {
		return self::$instance;
	}

	public static function printseconds($seconds) {
		$m = floor($seconds / 60);
		$s = floor($seconds % 60);
		return (($m < 10 ? "0" : "") . $m . ":" . ($s < 10 ? "0" : "") . (string)$s);
	}

	public function onEnable() {
		self::$instance = $this;

		$components = $this->getServer()->getPluginManager()->getPlugin("Components");
		if(!$components instanceof \core\Main) {
			throw new PluginException("Components plugin isn't loaded!");
		}

		$this->setCore($components);

		//$this->lobbyBossBar = new BossBar();
		//$this->lobbyBossBar->setText(LanguageUtils::translateColors("&l&eWelcome to &1C&ar&ea&6z&9e&5d&fC&7r&6a&cf&dt &6Duels&r"));
		$this->getServer()->setAutoSave(false);
		$this->getServer()->getDefaultLevel()->setAutoSave(false);
		Main::$spawnCoords = new Vector3(0.5, 93, 0.5);
		$this->setLobbyItems();
		$this->loadConfigs();
		$this->setArenaManager();
		$this->setDuelManager();
		$this->setNPCManager();
		$this->setKitManager();
		$this->setPartyManager();
		$this->registerGuiContainers();
		$this->registerUiWindows();
		$this->getServer()->getPluginManager()->registerEvents($this->listener = new EventListener($this), $this);
		$this->getServer()->getNetwork()->setName(LanguageUtils::translateColors("&1C&ar&ea&6z&9e&5d&fC&7r&6a&cf&dt &l&6Duels&r"));
		$this->getServer()->getNetwork()->updateName();
		$this->getCommand("duel")->setExecutor(new DuelCommand($this));
		$this->getCommand("hub")->setExecutor(new HubCommand($this));
		$this->getCommand("party")->setExecutor(new PartyCommand($this));
		$this->spawnInfoText();
	}

	public function loadConfigs() {
		if(!is_dir($this->getDataFolder())) @mkdir($this->getDataFolder());
		if(!is_dir($this->getDataFolder() . "data")) @mkdir($this->getDataFolder() . "data");
		$this->saveResource("skins" . DIRECTORY_SEPARATOR . "default.skin");

		$msgPath = $this->getDataFolder() . self::MESSAGES_FILE_PATH;
		if(!is_dir($msgPath)) @mkdir($msgPath);
		foreach(self::$languages as $lang => $filename) {
			$file = $msgPath . $filename;
			$this->saveResource(self::MESSAGES_FILE_PATH . $filename);
			if(!is_file($file)) {
				$this->getLogger()->warning("Couldn't find language file for '{$lang}'! Path: {$file}");
			} else {
				$this->getCore()->getLanguageManager()->registerLanguage($lang, (new Config($file, Config::JSON))->getAll());
			}
		}
	}

	public function spawnInfoText() {
		$pos = new Vector3(0.5, 94, -64.5);
		$level = $this->getServer()->getDefaultLevel();
		$this->infoText[] = new UpdatableFloatingText(new Position($pos->x, $pos->y + 1.2, $pos->z, $level), self::translateColors("&eYou're playing on &l&1C&ar&ea&6z&9e&5d&fC&7r&6a&cf&dt &6Duels"));
		$this->infoText["playing"] = new UpdatableFloatingText(new Position($pos->x, $pos->y + 0.9, $pos->z, $level), self::translateColors("&aThere are " . count($this->getServer()->getOnlinePlayers()) . " players online"));
		$this->infoText[] = new UpdatableFloatingText(new Position($pos->x, $pos->y + 0.2, $pos->z, $level), self::translateColors("&6Tap an NPC to join a match and start playing!"));
		$this->infoText["task"] = new UpdateInfoTextTask($this);
		$pos = new Vector3(-64.5, 94, 0.5);
		$this->infoText[] = new UpdatableFloatingText(new Position($pos->x, $pos->y + 0.3, $pos->z, $level), self::translateColors("&6Coming soon!"));
		$text = ["&a#BLAMEPOCKET", "&c#FORGEITUP", "&f#PLAYTIMELIMIT", "&aezz", "&6GG", "&cGG10 ezz noob skrub", "&eSpam &9@&bJackNoordhuis &e on twitter!"];
		$this->infoText[] = new UpdatableFloatingText(new Position($pos->x, $pos->y, $pos->z, $level), self::translateColors($text[array_rand($text)]));
	}

	public function setNPCManager() {
		if(isset($this->npcManager) and $this->npcManager instanceof NPCManager) return;
		$this->npcManager = new NPCManager($this);
	}

	public static function translateColors($string, $symbol = "&") {
		return LanguageUtils::translateColors($string, $symbol);
	}

	public function onDisable() {
		foreach($this->npcs as $npc) {
			$npc->active = false;
		}

		$this->duelManager->close();
		$this->partyManager->close();
		unset($this->sessionManager, $this->arenaManager, $this->npcManager, $this->duelManager);
	}

	public function getArenaManager() {
		return $this->arenaManager;
	}

	public function setArenaManager() {
		if(isset($this->arenaManager) and $this->arenaManager instanceof ArenaManager) return;
		$this->arenaManager = new ArenaManager($this);
	}

	public function getNPCMananger() {
		return $this->npcManager;
	}

	public function getDuelManager() {
		return $this->duelManager;
	}

	public function setDuelManager() {
		if(isset($this->duelManager) and $this->duelManager instanceof DuelManager) return;
		$this->duelManager = new DuelManager($this);
	}

	public function getKitManager() {
		return $this->kitManager;
	}

	public function setKitManager() {
		if(isset($this->kitManager) and $this->kitManager instanceof KitManager) return;
		$this->kitManager = new KitManager($this);
	}

	/**
	 * @return PartyManager
	 */
	public function getPartyManager() : PartyManager {
		return $this->partyManager;
	}

	public function setPartyManager() {
		if(isset($this->partyManager) and $this->partyManager instanceof PartyManager) return;
		$this->partyManager = new PartyManager($this);
	}

	public function getPlayingCount($type) {
		$count = 0;
		/** @var Duel $duel */
		foreach($this->duelManager->getAll() as $duel) {
			if(!$duel instanceof Duel and $duel->getType() != $type and !$duel->hasEnded()) continue;
			$count += count($duel->getPlayers());
		}
		return $count;
	}

	/** @var Item[] */
	protected $lobbyItems = [];

	/**
	 * Set the lobby items
	 */
	public function setLobbyItems() {
		$this->lobbyItems = [
			Item::get(Item::AIR),
			new KitSelector(),
			new PartyEventSelector(),
			new DuelKitRequestSelector(),
			Item::get(Item::AIR),
			Item::get(Item::AIR),
			new SpawnWarpItem(),
			Item::get(Item::AIR),
			new ServerSelector(),
		];
	}

	/**
	 * Give a player the lobby items
	 *
	 * @param Player $player
	 */
	public function giveLobbyItems(Player $player) {
		self::giveItems($player, $this->lobbyItems, true);
	}

	/**
	 * Give a player an array of items and order them correctly in their hot bar
	 *
	 * @param Player $player
	 * @param Item[] $items
	 * @param bool $shouldCloneItems
	 */
	public static function giveItems(Player $player, array $items, $shouldCloneItems = false) {
		for($i = 0, $invIndex = 0, $inv = $player->getInventory(), $itemCount = count($items); $i < $itemCount; $i++, $invIndex++) {
			$inv->setItem($invIndex, ($shouldCloneItems ? clone $items[$i] : $items[$i]));
		}
		$inv->sendContents($player);
	}

	/**
	 * Register the custom GUI containers
	 */
	protected function registerGuiContainers() {
		$containers = [
			[new PlayDuelTypeSelectionContainer($this), PlayDuelTypeSelectionContainer::CONTAINER_ID],
			[new PlayKitSelectionContainer($this), PlayKitSelectionContainer::CONTAINER_ID],
			[new DuelRequestDuelTypeSelectionContainer($this), DuelRequestDuelTypeSelectionContainer::CONTAINER_ID],
			[new DuelRequestKitSelectionContainer($this), DuelRequestKitSelectionContainer::CONTAINER_ID],
		];

		$guiManager = $this->getCore()->getGuiManager();
		foreach($containers as $container) {
			try {
				$guiManager->registerContainer($container[0], $container[1], true);
			} catch(\ErrorException $e) {
				$this->getLogger()->debug("Failed to register container class '" . (new \ReflectionObject($container[0]))->getShortName() . "''!");
				$this->getLogger()->logException($e);
			}
		}
	}

	/**
	 * Register the custom UI windows
	 */
	protected function registerUiWindows() {
		$forms = [
			[new PlayDuelTypeSelectionForm(), PlayDuelTypeSelectionForm::FORM_UI_ID],
			[new PlayKitSelectionForm(), PlayKitSelectionForm::FORM_UI_ID],
			[new DuelRequestDuelTypeSelectionForm(), DuelRequestDuelTypeSelectionForm::FORM_UI_ID],
			[new DuelRequestKitSelectionForm(), DuelRequestKitSelectionForm::FORM_UI_ID],
		];

		$uiManager = $this->getCore()->getUiManager();
		foreach($forms as $form) {
			try {
				$uiManager->registerForm($form[0], $form[1], true);
			} catch(\ErrorException $e) {
				$this->getLogger()->debug("Failed to register form class '" . (new \ReflectionObject($form[0]))->getShortName() . "''!");
				$this->getLogger()->logException($e);
			}
		}
	}

	protected function spawnNPCs() : void {
		$data = $this->npcData();
		foreach($data as $npc) {
			$nbt = Entity::createBaseNBT($npc["pos"], null, $npc["rotation"]["yaw"], $npc["rotation"]["pitch"]);
			$nbt->setString("customName", new StringTag("customName", $npc["name"]));

			/** @var SelectionNPC $entity */
			$entity = Entity::createEntity("SelectionNPC", $level = $this->getServer()->getDefaultLevel(), $nbt);
			$entity->setType($npc["type"]);
			$entity->showPlaying(0);
			$chunk = $level->getChunk($entity->getX(), $entity->getZ());
			$level->registerChunkLoader($entity, $chunk->getX(), $chunk->getZ());
			$entity->spawnToAll();
		}
	}

	private function npcData() : array {
		return [
			[
				"name" => TextFormat::YELLOW . "» Play - 1v1 «",
				"pos" => new Vector3(0.5, 96, 69.5),
				"rotation" => ["yaw" => 180, "pitch" => 0,],
				"skin" => "default",
				"type" => DuelType::DUEL_TYPE_1V1
			],
			[
				"name" => TextFormat::YELLOW . "» Play - 2v2 «",
				"pos" => new Vector3(69.5, 96, 0.5),
				"rotation" => ["yaw" => 90, "pitch" => 0,],
				"skin" => "default",
				"type" => DuelType::DUEL_TYPE_2v2
			]
		];
	}

}