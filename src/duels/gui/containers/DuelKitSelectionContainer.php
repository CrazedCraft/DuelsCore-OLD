<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 20/4/17
 * Time: 9:18 PM
 */

namespace duels\gui\containers;

use core\CorePlayer;
use core\gui\item\GUIItem;
use duels\DuelsPlayer;
use duels\gui\item\kit\KitGUIItem;
use duels\Main;
use duels\session\PlayerSession;
use pocketmine\utils\TextFormat as TF;

class DuelKitSelectionContainer extends KitSelectionContainer {

	const CONTAINER_ID = "duel_selection_container";

	public function onSelect(int $slot, GUIItem $item, CorePlayer $player) : bool {
		$player->removeWindow($this);
		if(!$item instanceof KitGUIItem) {
			throw new \InvalidArgumentException("Expected duels/gui/item/kit/KitGUIItem, got core/gui/item/GUIItem instead");
		}
		$plugin = Main::getInstance();
		$pSession = $sSession = $plugin->sessionManager->get($player->getName());
		if($pSession instanceof PlayerSession) {
			$entity = $pSession->lastTapped;
			if($entity instanceof DuelsPlayer) {
				$eSession = $rSession = $plugin->sessionManager->get($entity->getName());
				if($eSession instanceof PlayerSession) {
					$pSession->lastSelectedKit = $item->getKit();
					$rSession->addRequest($player, $entity, $item->getName());
					$player->sendMessage(TF::AQUA . "Sent a Duel request to " . TF::BOLD . TF::GREEN . $entity->getName() . TF::RESET . TF::AQUA . "!");
				}
			} else {
				$player->sendMessage(TF::GOLD . "Could not send duel request");
			}
		}
		return false; // don't remove the item
	}

}