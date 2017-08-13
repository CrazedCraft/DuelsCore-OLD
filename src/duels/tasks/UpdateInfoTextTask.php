<?php

namespace duels\tasks;

use core\language\LanguageUtils;
use duels\Main;
use pocketmine\scheduler\PluginTask;

/**
 * Task that updates the info text in the lobby every 30 seconds
 */
class UpdateInfoTextTask extends PluginTask  {

	/** @var Main */
	private $plugin;

	/** @var string[] */
	private $text = [];

	public function __construct(Main $plugin) {
		parent::__construct($plugin);
		$this->plugin = $plugin;
		$this->setHandler($plugin->getServer()->getScheduler()->scheduleRepeatingTask($this, 20 * 30));
		//$this->text = [
		//	LanguageUtils::translateColors("&eMake sure to follow us on twitter! &l&9@&bCrazedCraft&r&e!&r"),
		//	LanguageUtils::translateColors("&6Make sure to follow us on twitter! &l&9@&bConflictPE&r&6!&r"),
		//	LanguageUtils::translateColors("&eMake sure to follow our manager on twitter! &l&9@&bJackNoordhuis&r&e!&r"),
		//	LanguageUtils::translateColors("&6Make sure to follow our owner on twitter! &l&9@&bRustyMCPE&r&6!&r"),
		//	LanguageUtils::translateColors("&6Make sure to checkout our prison server &l&epsn.ConflictPE.net&r&6!&r"),
		//	LanguageUtils::translateColors("&eSupport us by buying a rank at &l&cStore.ConflictPE.net&r&e&r"),
		//];
	}

	public function onRun($currentTick) {
		$this->plugin->infoText["playing"]->update(Main::translateColors("&aThere are " . count($this->plugin->getServer()->getOnlinePlayers()) . " players online"));
		foreach($this->plugin->getNPCMananger()->NPCs as $npc) {
			$npc->showPlaying($this->plugin->getPlayingCount($npc->getType()));
		}
		//$this->plugin->lobbyBossBar->setText(LanguageUtils::translateColors($this->text[array_rand($this->text)]));
	}

}