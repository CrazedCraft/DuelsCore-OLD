<?php

namespace duels\session;

use core\CorePlayer;
use duels\duel\Duel;
use duels\kit\Kit;
use duels\Main;
use duels\party\Party;
use duels\tasks\RequestTask;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;

class PlayerSession {

	/* States */
	const STATUS_WAITING = 0;
	const STATUS_COUNTDOWN = 1;
	const STATUS_PLAYING = 2;

	/** @var null|CorePlayer */
	public $lastTapped = null;

	/** @var null|Kit */
	public $lastSelectedKit = null;

	/** @var bool */
	public $requestsStatus = true;

	/** @var array */
	public $requests = [];

	/** @var Player */
	private $player;

	/** @var string */
	private $name = "";

	/** @var int */
	private $status;

	/** @var null|Duel */
	private $duel;

	/** @var array */
	private $team;

	/** @var string|null */
	private $partyId = null;

	/** @var null|string */
	public $lastSelectedPartyType = null;

	/** @var bool */
	protected $active = true;

	public function __construct(Player $player, $status = self::STATUS_WAITING, $duel = null) {
		$this->player = $player;
		$this->status = $status;
		$this->duel = $duel;
	}

	public function isActive() {
		return $this->active;
	}

	public function recalculateName() {
		$this->name = $this->player->getName();
	}

	public function getPlayer() {
		return $this->player;
	}

	public function getStatus() {
		if(!isset($this->status)) $this->status = self::STATUS_WAITING;
		return $this->status;
	}

	public function setStatus($status) {
		$this->status = $status;
	}

	public function getDuel() {
		return $this->duel;
	}

	public function setDuel(Duel $duel) {
		$this->duel = $duel;
	}

	public function removeDuel() {
		$this->status = self::STATUS_WAITING;
		$this->duel = null;
	}

	public function getTeam() {
		if(!$this->inTeam()) return null;
		return $this->team;
	}

	public function setTeam($name) {
		$this->team = $name;
	}

	public function inTeam() {
		return isset($this->team) and $this->team !== "";
	}

	public function removeTeam() {
		if(!$this->inTeam()) return;
		unset($this->team);
	}

	public function addRequest(Player $requester, Player $player, $kitName = "") {
		$this->requests[$requester->getName()] = new RequestTask(Main::getInstance(), $player, $requester);
		$player->sendMessage(TF::AQUA . "Duel request from " . TF::BOLD . TF::GOLD . $requester->getName() . TF::RESET . TF::AQUA . ($kitName !== "" ? " with the {$kitName}" . TF::RESET . TF::AQUA : "") . " type " . TF::GOLD . "/duel " . $requester->getName() . TF::AQUA . " to accept!");
	}

	public function setRequestStatus($value = true) {
		$this->requestsStatus = $value;
	}

	public function getRequest($from) {
		if(!$this->hasRequest($from)) return;
		return $this->requests[$from];
	}

	public function hasRequest($from) {
		return isset($this->requests[$from]);
	}

	public function removeRequest($name) {
		if(!$this->hasRequest($name)) return;
		Server::getInstance()->getScheduler()->cancelTask($this->requests[$name]->getTaskId());
		unset($this->requests[$name]);
	}

	public function close() {
		if($this->active) {
			$this->active = false;
			if($this->inDuel() and $this->player instanceof Player) {
				$this->duel->removePlayer($this->player->getName());
			}
			unset($this->team);
			unset($this->player);
			unset($this->status);
			unset($this->duel);
		}
	}

	public function inDuel() {
		return isset($this->duel) and $this->duel instanceof Duel;
	}

	public function inParty() {
		return $this->partyId !== null and Main::getInstance()->getPartyManager()->getParty($this->partyId) instanceof Party;
	}

	public function getParty() {
		return Main::getInstance()->getPartyManager()->getParty($this->partyId);
	}

	public function setParty(Party $party) {
		$this->partyId = $party->getId();
	}

	public function removeParty() {
		$this->partyId = null;
	}

}