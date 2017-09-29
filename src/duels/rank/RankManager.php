<?php

namespace duels\rank;

use duels\Main;

class RankManager {

	public $ranks = [];
	private $plugin;
	private $data = [];

	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
		$this->data = ["owner" => ["name" => "Owner", "format" => "&7[&l&bOwner&r&7]", "perms" => ["pocketmine.command.gamemode", "pocketmine.command.say"]], "admin" => ["name" => "Admin", "format" => "&5[&7&bAdmin&r&5]", "perms" => ["pocketmine.command.gamemode", "pocketmine.command.say"]], "vip" => ["name" => "VIP", "format" => "&7[&3&lVIP&r&7]", "perms" => []], "mod" => ["name" => "Owner", "format" => "&5[&l&3Mod&r&5]", "perms" => ["pocketmine.command.gamemode", "pocketmine.command.say"]], "builder" => ["name" => "Owner", "format" => "&5[&l&aBuilder&r&5]", "perms" => ["pocketmine.command.gamemode", "pocketmine.command.say"]], "youtube" => ["name" => "Owner", "format" => "&5[&l&cYou&fTube&r&5]", "perms" => ["pocketmine.command.gamemode", "pocketmine.command.say"]]];
		$this->register();
	}

	public function register() {
		foreach($this->data as $key => $rank) {
			$this->ranks[$key] = new Rank($rank["name"], $rank["format"], $rank["perms"]);
		}
	}

	public function get($name) {
		if(!$this->isRank($name)) return null;
		return $this->ranks[$name];
	}

	public function isRank($name) {
		return isset($this->ranks[$name]) and $this->ranks[$name] instanceof Rank;
	}

}
