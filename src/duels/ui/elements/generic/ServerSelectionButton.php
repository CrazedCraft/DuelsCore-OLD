<?php
/**
 * DuelsCore – ServerSelectionButton.php
 *
 * Copyright (C) 2017 Jack Noordhuis
 *
 * This is private software, you cannot redistribute and/or modify it in any way
 * unless given explicit permission to do so. If you have not been given explicit
 * permission to view or modify this software you should take the appropriate actions
 * to remove this software from your device immediately.
 *
 * @author Jack Noordhuis
 *
 * Created on 13/9/17 at 6:14 PM
 *
 */

namespace duels\ui\elements\generic;

use core\CorePlayer;
use core\language\LanguageUtils;
use core\network\NetworkServer;
use pocketmine\customUI\elements\simpleForm\Button;

abstract class ServerSelectionButton extends Button {

	const SERVER_ID_INVALID = -1;  // an invalid server id that will be used to pick a random server

	/** @var string */
	private $node = "";

	/** @var int */
	private $serverId = self::SERVER_ID_INVALID;

	public function __construct(string $text, string $node, int $serverId = self::SERVER_ID_INVALID, string $imgFile = "0-0.png") {
		$this->node = $node;
		$this->serverId = $serverId;
		parent::__construct(LanguageUtils::translateColors($text));
		$this->addImage(Button::IMAGE_TYPE_URL, "http://jacknoordhuis.net/minecraft/icons/items/{$imgFile}");
	}

	/**
	 * @return string
	 */
	public function getNode() : string {
		return $this->node;
	}

	/**
	 * @return int
	 */
	public function getServerId() : int {
		return $this->serverId;
	}

	/**
	 * Transfer player to specific server or suitable server from node
	 *
	 * @param CorePlayer $player
	 */
	public function transferToSuitableServer(CorePlayer $player) {
		$currentServer = $player->getCore()->getNetworkManager()->getServer();
		$node = $player->getCore()->getNetworkManager()->getNodes()[$this->node];
		if($this->serverId !== self::SERVER_ID_INVALID) {
			$server = $node->getServers()[$this->serverId];
		} else {
			$server = $node->getSuitableServer();
		}

		if($server instanceof NetworkServer) {
			if($server->getNode() !== $currentServer->getNode() and $server->getId() !== $currentServer->getId()) {
				if($server->isAvailable()) {
					$player->transfer($server->getHost(), $server->getPort());
				} else {
					$player->sendMessage(LanguageUtils::translateColors("&r{$this->node}-{$node->getDisplay()} is currently unavailable!"));
				}
			} else {
				$player->sendMessage(LanguageUtils::translateColors("&rYou're currently connected to that server!"));
			}
		} else {
			$player->sendMessage(LanguageUtils::translateColors("&rThere are currently no {$node->getDisplay()} servers online!"));
		}
	}

	/**
	 * Handle button click
	 *
	 * @param bool $value
	 * @param CorePlayer $player
	 */
	public function handle($value, $player) {
		$this->transferToSuitableServer($player);
	}

}