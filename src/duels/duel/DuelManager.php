<?php

namespace duels\duel;

use duels\arena\Arena;
use duels\kit\Kit;
use duels\Main;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class DuelManager {

	public $duels = [];
	private $plugin;

	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
	}

	public function getAll() {
		return $this->duels;
	}

	public function isDuel($id) {
		return isset($this->duels[$id]);
	}

	public function removeDuel($id) {
		if(!$this->getDuel($id)) return;
		unset($this->duels[$id]);
	}

	public function getDuel($id) {
		return isset($this->duels[$id]) and $this->duels[$id] instanceof Duel;
	}

	public function findDuel(Player $player, $type, Kit $kit = null, $checkOs = false) {
		foreach($this->duels as $duel) {
			if($duel->isJoinable()) {
				if($duel->getType() === $type) {
					if($checkOs and !$duel->matchesOs($player->getDeviceOS())) continue;
					if($kit instanceof Kit and $kit->getName() != $duel->getKit()->getName()) continue;
					$duel->addPlayer($player);
					return $this->plugin->sessionManager->get($player->getName())->setDuel($duel);
				} else {
					continue;
				}
			} else {

			}
		}
		if(count($this->duels) <= 30) {
			$this->addDuel($type, $kit, $player->getDeviceOS());
			foreach($this->duels as $duel) {
				if($duel->isJoinable()) {
					if($duel->getType() === $type) {
						if($checkOs and !$duel->matchesOs($player->getDeviceOS())) continue;
						if($kit instanceof Kit and $kit->getName() != $duel->getKit()->getName()) continue;
						$duel->addPlayer($player);
						return $this->plugin->sessionManager->get($player->getName())->setDuel($duel);
					} else {
						continue;
					}
				} else {

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
		$this->duels[$arena->getId()] = $duel = new Duel($this->plugin, $type, $arena, ($kit instanceof Kit ? $kit : $this->plugin->getKitManager()->findRandom()));
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