<?php

declare(strict_types=1);

namespace duels\duel;

use core\CorePlayer;
use core\Utils;
use pocketmine\utils\TextFormat;

class Team {

	/** @var Duel */
	private $battle;

	/** @var int */
	private $playerCount = 0;

	/** @var string[] */
	private $players = [];

	/** @var string */
	private $name = "";

	/** @var int */
	private $color;

	/** @var string */
	private $chatColor = TextFormat::RESET;

	/** @var bool */
	private $closed = false;

	public function __construct(Duel $battle, string $name, int $color, string $chatColor) {
		$this->battle = $battle;
		$this->name = $name;
		$this->color = $color;
		$this->chatColor = $chatColor;
	}

	/**
	 * Get all players on the team
	 *
	 * @return CorePlayer[]
	 */
	public function getPlayers() : array {
		$new = [];
		foreach($this->players as $name => $uuid) {
			$new[$name] = Utils::getPlayerByUUID($uuid);
		}

		return $new;
	}

	/**
	 * Check if a player is on this team
	 *
	 * @param CorePlayer $player
	 *
	 * @return bool
	 */
	public function playerOnTeam(CorePlayer $player) : bool {
		return isset($this->players[$player->getName()]);
	}

	/**
	 * Add a player to the team
	 *
	 * @param CorePlayer $player
	 */
	public function addPlayer(CorePlayer $player) : void {
		$this->players[$player->getName()] = $player->getUniqueId()->toString();
		$this->playerCount++;
	}

	/**
	 * Remove a player from the team
	 *
	 * @param string $name
	 */
	public function removePlayer(string $name) : void {
		unset($this->players[$name]);
		$this->playerCount--;
	}

	/**
	 * Get the number of players on the team
	 *
	 * @return int
	 */
	public function getPlayerCount() : int {
		return $this->playerCount;
	}

	/**
	 * Get this teams name
	 *
	 * @return string
	 */
	public function getName() : string {
		return $this->name;
	}

	/**
	 * Get this teams color id
	 *
	 * @return int
	 */
	public function getColor() : int {
		return $this->color;
	}

	/**
	 * Get this teams chat color
	 *
	 * @return string
	 */
	public function getChatColor() : string {
		return $this->chatColor;
	}

	/**
	 * Broadcast a message to all players on the team
	 *
	 * @param string $message
	 */
	public function broadcastMessage(string $message) : void {
		foreach($this->players as $name => $uuid) {
			Utils::getPlayerByUUID($uuid)->sendMessage($message);
		}
	}

	/**
	 * Check if the team still has players
	 *
	 * @return bool
	 */
	public function hasPlayers() : bool {
		return $this->playerCount <= 0;
	}

	/**
	 * Check if the team is closed
	 *
	 * @return bool
	 */
	public function closed() : bool {
		return $this->closed;
	}

	/**
	 * Safely close the team
	 */
	public function close() : void {
		if(!$this->closed) {
			$this->closed = true;

			foreach($this->players as $name => $uuid) {
				unset($this->players[$name]);
			}

			$this->players = [];

			unset($this->battle);
		}
	}

}