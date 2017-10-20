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
use core\Main;
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
		parent::__construct(LanguageUtils::translateColors($text . "&r"));
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
	 * @return string
	 */
	public function getText() : string {
		return $this->text . " " . $this->getOnlineStatus();
	}

	/**
	 * Get the online status to be displayed in the button text
	 *
	 * @return string
	 */
	protected function getOnlineStatus() : string {
		$networkManager = Main::getInstance()->getNetworkManager();
		$currentServer = $networkManager->getMap()->getServer();
		$node = $networkManager->getMap()->getNodes()[$this->node];
		if($this->serverId !== self::SERVER_ID_INVALID) {
			$server = $node->getServers()[$this->serverId];
		} else {
			$server = $node->getSuitableServer();
		}

		if($server instanceof NetworkServer) {
			if($server->getNetworkId() !== $currentServer->getNetworkId()) {
				if($server->isOnline() and time() - $server->getLastSyncTime() < 100) {
					return LanguageUtils::translateColors("&7(&d{$server->getOnlinePlayers()}&0/&5{$server->getMaxPlayers()}&7)");
				} else {
					return LanguageUtils::translateColors("&7(&4offline&7)");
				}
			} else {
				return LanguageUtils::translateColors("&7(&9Connected&7)");
			}
		} elseif($this->node === $currentServer->getNode() and $this->serverId === $currentServer->getId()) {
			return LanguageUtils::translateColors("&7(&9Connected&7)");
		} else {
			return LanguageUtils::translateColors("&7(&4offline&7)");
		}
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
			if($server->getNetworkId() !== $currentServer->getNetworkId()) {
				if($server->isOnline() and time() - $server->getLastSyncTime() < 100) {
					$player->transfer($server->getHost(), $server->getPort());
				} else {
					$player->sendMessage(LanguageUtils::translateColors("&c{$node->getName()}-{$this->serverId}&6 is currently unavailable!"));
				}
			} else {
				$player->sendMessage(LanguageUtils::translateColors("&6You're currently connected to that server!"));
			}
		} elseif($this->node === $currentServer->getNode() and $this->serverId === $currentServer->getId()) {
			$player->sendMessage(LanguageUtils::translateColors("&6You're currently connected to that server!"));
		} else {
			$player->sendMessage(LanguageUtils::translateColors("&6There are currently no &c{$node->getDisplay()}&6 servers online!"));
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