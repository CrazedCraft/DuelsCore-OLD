<?php

declare(strict_types=1);

namespace duels\kit;

use pocketmine\item\Item;

class KitData {

	/**
	 * Dump all the old kit data
	 *
	 * @return array
	 */
	public static function getKitData() : array {
		//return [
		//	[
		//		"name" => TF::BOLD . TF::DARK_AQUA . "Random",
		//		"display-item" => Item::get(Item::DIAMOND_AXE),
		//		"image-file" => "279-0.png",
		//		"type" => "random_kit",
		//		"description" => TF::ITALIC . TF::GRAY . "Tap on the ground to pick a random kit!",
		//		"items" => [],
		//		"armor" => [],
		//	],
		//	[
		//		"name" => TF::BOLD . TF::GREEN . "UHC",
		//		"display-item" => Item::get(Item::GOLDEN_APPLE),
		//		"image-file" => "322-0.png",
		//		"type" => "kit_select",
		//		"items" => [
		//			self::getEnchantedItem(Item::get(Item::DIAMOND_SWORD), [Enchantment::getEnchantment(Enchantment::TYPE_WEAPON_SHARPNESS)->setLevel(5)]),
		//			Item::get(Item::SNOWBALL, 0, 16),
		//			self::getEnchantedItem(Item::get(Item::BOW), [Enchantment::getEnchantment(Enchantment::TYPE_BOW_POWER)->setLevel(4)]),
		//			Item::get(Item::GOLDEN_APPLE, 0, 3),
		//			Item::get(0),
		//			Item::get(Item::STEAK, 0, 64),
		//			Item::get(0),
		//			Item::get(Item::ARROW, 0, 64)],
		//		"armor" => [
		//			self::getEnchantedItem(Item::get(Item::DIAMOND_HELMET), [Enchantment::getEnchantment(Enchantment::TYPE_ARMOR_PROJECTILE_PROTECTION)->setLevel(1)]),
		//			Item::get(Item::IRON_CHESTPLATE),
		//			self::getEnchantedItem(Item::get(Item::IRON_LEGGINGS), [Enchantment::getEnchantment(Enchantment::TYPE_ARMOR_EXPLOSION_PROTECTION)->setLevel(1)]),
		//			self::getEnchantedItem(Item::get(Item::DIAMOND_BOOTS), [Enchantment::getEnchantment(Enchantment::TYPE_ARMOR_FALL_PROTECTION)->setLevel(1)]),
		//		],
		//	],
		//	[
		//		"name" => TF::BOLD . TF::GREEN . "Archer",
		//		"display-item" => Item::get(Item::BOW),
		//		"image-file" => "261-0.png",
		//		"type" => "kit_select",
		//		"items" => [
		//			self::getEnchantedItem(Item::get(Item::BOW), [Enchantment::getEnchantment(Enchantment::TYPE_BOW_KNOCKBACK)->setLevel(2), Enchantment::getEnchantment(Enchantment::TYPE_BOW_INFINITY)]),
		//			Item::get(Item::ARROW),
		//			Item::get(0),
		//			Item::get(0),
		//			Item::get(0),
		//			Item::get(Item::STEAK, 0, 64),
		//		],
		//		"armor" => [
		//			Item::get(Item::LEATHER_CAP),
		//			Item::get(Item::LEATHER_TUNIC),
		//			Item::get(Item::LEATHER_PANTS),
		//			Item::get(Item::LEATHER_BOOTS),
		//		],
		//	],
		//	[
		//		"name" => TF::BOLD . TF::GREEN . "Combo",
		//		"display-item" => Item::get(Item::PUFFERFISH),
		//		"image-file" => "349-3.png",
		//		"type" => "kit_select",
		//		"items" => [
		//			self::getEnchantedItem(Item::get(Item::DIAMOND_SWORD), [Enchantment::getEnchantment(Enchantment::TYPE_WEAPON_SHARPNESS)->setLevel(2), Enchantment::getEnchantment(Enchantment::TYPE_WEAPON_FIRE_ASPECT)->setLevel(2), Enchantment::getEnchantment(Enchantment::TYPE_MINING_DURABILITY)->setLevel(10)]),
		//			Item::get(Item::ENCHANTED_GOLDEN_APPLE, 0, 64),
		//			Item::get(Item::POTION, Potion::STRENGTH_T),
		//			Item::get(Item::POTION, Potion::SWIFTNESS_TWO),
		//			Item::get(Item::POTION, Potion::STRENGTH_T),
		//			Item::get(Item::POTION, Potion::SWIFTNESS_TWO),
		//			Item::get(0),
		//			Item::get(Item::STEAK, 0, 64),
		//		],
		//		"armor" => [
		//			self::getEnchantedItem(Item::get(Item::DIAMOND_HELMET), [Enchantment::getEnchantment(Enchantment::TYPE_ARMOR_PROTECTION)->setLevel(4), Enchantment::getEnchantment(Enchantment::TYPE_MINING_DURABILITY)->setLevel(10)]),
		//			self::getEnchantedItem(Item::get(Item::DIAMOND_CHESTPLATE), [Enchantment::getEnchantment(Enchantment::TYPE_ARMOR_PROTECTION)->setLevel(4), Enchantment::getEnchantment(Enchantment::TYPE_MINING_DURABILITY)->setLevel(10)]),
		//			self::getEnchantedItem(Item::get(Item::DIAMOND_LEGGINGS), [Enchantment::getEnchantment(Enchantment::TYPE_ARMOR_PROTECTION)->setLevel(4), Enchantment::getEnchantment(Enchantment::TYPE_MINING_DURABILITY)->setLevel(10)]),
		//			self::getEnchantedItem(Item::get(Item::DIAMOND_BOOTS), [Enchantment::getEnchantment(Enchantment::TYPE_ARMOR_PROTECTION)->setLevel(4), Enchantment::getEnchantment(Enchantment::TYPE_MINING_DURABILITY)->setLevel(10)]),
		//		],
		//	],
		//	[
		//		"name" => TF::BOLD . TF::GREEN . "Gapple",
		//		"display-item" => Item::get(Item::ENCHANTED_GOLDEN_APPLE),
		//		"image-file" => "466-0.gif",
		//		"type" => "kit_select",
		//		"items" => [
		//			self::getEnchantedItem(Item::get(Item::DIAMOND_SWORD), [Enchantment::getEnchantment(Enchantment::TYPE_WEAPON_SHARPNESS)->setLevel(5), Enchantment::getEnchantment(Enchantment::TYPE_WEAPON_FIRE_ASPECT)->setLevel(2), Enchantment::getEnchantment(Enchantment::TYPE_MINING_DURABILITY)->setLevel(3)]),
		//			Item::get(Item::ENCHANTED_GOLDEN_APPLE, 0, 64),
		//			Item::get(0),
		//			Item::get(0),
		//			Item::get(Item::STEAK, 0, 64),
		//		],
		//		"armor" => [
		//			self::getEnchantedItem(Item::get(Item::DIAMOND_HELMET), [Enchantment::getEnchantment(Enchantment::TYPE_ARMOR_PROTECTION)->setLevel(4), Enchantment::getEnchantment(Enchantment::TYPE_MINING_DURABILITY)->setLevel(3)]),
		//			self::getEnchantedItem(Item::get(Item::DIAMOND_CHESTPLATE), [Enchantment::getEnchantment(Enchantment::TYPE_ARMOR_PROTECTION)->setLevel(4), Enchantment::getEnchantment(Enchantment::TYPE_MINING_DURABILITY)->setLevel(3)]),
		//			self::getEnchantedItem(Item::get(Item::DIAMOND_LEGGINGS), [Enchantment::getEnchantment(Enchantment::TYPE_ARMOR_PROTECTION)->setLevel(4), Enchantment::getEnchantment(Enchantment::TYPE_MINING_DURABILITY)->setLevel(3)]),
		//			self::getEnchantedItem(Item::get(Item::DIAMOND_BOOTS), [Enchantment::getEnchantment(Enchantment::TYPE_ARMOR_PROTECTION)->setLevel(4), Enchantment::getEnchantment(Enchantment::TYPE_MINING_DURABILITY)->setLevel(3)]),
		//		],
		//	],
		//	[
		//		"name" => TF::BOLD . TF::GREEN . "Diamond",
		//		"display-item" => Item::get(Item::DIAMOND_HELMET),
		//		"image-file" => "310-0.png",
		//		"type" => "kit_select",
		//		"items" => [
		//			Item::get(Item::DIAMOND_SWORD),
		//			Item::get(Item::BOW),
		//			Item::get(0),
		//			Item::get(Item::STEAK, 0, 64),
		//			Item::get(0),
		//			Item::get(Item::ARROW, 0, 32),
		//		],
		//		"armor" => [
		//			self::getEnchantedItem(Item::get(Item::DIAMOND_HELMET), [Enchantment::getEnchantment(Enchantment::TYPE_ARMOR_PROJECTILE_PROTECTION)]),
		//			self::getEnchantedItem(Item::get(Item::DIAMOND_CHESTPLATE), [Enchantment::getEnchantment(Enchantment::TYPE_ARMOR_PROJECTILE_PROTECTION)]),
		//			self::getEnchantedItem(Item::get(Item::DIAMOND_LEGGINGS), [Enchantment::getEnchantment(Enchantment::TYPE_ARMOR_PROJECTILE_PROTECTION)]),
		//			self::getEnchantedItem(Item::get(Item::DIAMOND_BOOTS), [Enchantment::getEnchantment(Enchantment::TYPE_ARMOR_PROJECTILE_PROTECTION), Enchantment::getEnchantment(Enchantment::TYPE_ARMOR_FALL_PROTECTION)->setLevel(4)]),
		//		],
		//	],
		//	[
		//		"name" => TF::BOLD . TF::GREEN . "Iron",
		//		"display-item" => Item::get(Item::IRON_HELMET),
		//		"image-file" => "306-0.png",
		//		"type" => "kit_select",
		//		"items" => [
		//			Item::get(Item::IRON_SWORD),
		//			Item::get(Item::BOW),
		//			Item::get(0),
		//			Item::get(Item::STEAK, 0, 64),
		//			Item::get(0),
		//			Item::get(0),
		//			Item::get(Item::ARROW, 0, 32),
		//		],
		//		"armor" => [
		//			Item::get(Item::IRON_HELMET),
		//			Item::get(Item::IRON_CHESTPLATE),
		//			Item::get(Item::IRON_LEGGINGS),
		//			self::getEnchantedItem(Item::get(Item::IRON_BOOTS), [Enchantment::getEnchantment(Enchantment::TYPE_ARMOR_FALL_PROTECTION)->setLevel(4)]),
		//		],
		//	],
		//	[
		//		"name" => TF::BOLD . TF::GREEN . "SG",
		//		"display-item" => Item::get(Item::FISHING_ROD),
		//		"image-file" => "346-0.png",
		//		"type" => "kit_select",
		//		"items" => [
		//			Item::get(272, 0, 1),
		//			Item::get(261, 0, 1),
		//			Item::get(322, 0, 1),
		//			Item::get(400, 0, 2),
		//			Item::get(360, 0, 2),
		//			Item::get(297, 0, 1),
		//			Item::get(262, 0, 8)
		//		],
		//		"armor" => [
		//			Item::get(314),
		//			Item::get(307),
		//			Item::get(304),
		//			Item::get(309)
		//		],
		//	],
		//	[
		//		"name" => TF::BOLD . TF::GREEN . "Iron Soup",
		//		"display-item" => Item::get(Item::MUSHROOM_STEW),
		//		"image-file" => "282-0.png",
		//		"type" => "kit_select",
		//		"items" => [
		//			self::getEnchantedItem(Item::get(Item::DIAMOND_SWORD), [Enchantment::getEnchantment(Enchantment::TYPE_WEAPON_SHARPNESS)->setLevel(3)]),
		//			Item::get(Item::MUSHROOM_STEW),
		//			Item::get(Item::MUSHROOM_STEW),
		//			Item::get(Item::MUSHROOM_STEW),
		//			Item::get(Item::MUSHROOM_STEW),
		//			Item::get(Item::MUSHROOM_STEW),
		//			Item::get(Item::MUSHROOM_STEW),
		//			Item::get(Item::MUSHROOM_STEW),
		//			Item::get(Item::MUSHROOM_STEW),
		//			Item::get(Item::MUSHROOM_STEW),
		//			Item::get(Item::MUSHROOM_STEW),
		//			Item::get(Item::MUSHROOM_STEW),
		//			Item::get(Item::MUSHROOM_STEW),
		//			Item::get(Item::MUSHROOM_STEW),
		//			Item::get(Item::MUSHROOM_STEW),
		//			Item::get(Item::MUSHROOM_STEW),
		//			Item::get(Item::MUSHROOM_STEW),
		//			Item::get(Item::MUSHROOM_STEW),
		//			Item::get(Item::MUSHROOM_STEW),
		//		],
		//		"armor" => [
		//			self::getEnchantedItem(Item::get(Item::IRON_HELMET), [Enchantment::getEnchantment(Enchantment::TYPE_ARMOR_PROTECTION)->setLevel(2)]),
		//			self::getEnchantedItem(Item::get(Item::IRON_CHESTPLATE), [Enchantment::getEnchantment(Enchantment::TYPE_ARMOR_PROTECTION)->setLevel(2)]),
		//			self::getEnchantedItem(Item::get(Item::IRON_LEGGINGS), [Enchantment::getEnchantment(Enchantment::TYPE_ARMOR_PROTECTION)->setLevel(2)]),
		//			self::getEnchantedItem(Item::get(Item::IRON_BOOTS), [Enchantment::getEnchantment(Enchantment::TYPE_ARMOR_PROTECTION)->setLevel(2)]),
		//		],
		//	],
		//	[
		//		"name" => TF::BOLD . TF::GREEN . "Axe",
		//		"display-item" => Item::get(Item::IRON_AXE),
		//		"image-file" => "258-0.png",
		//		"type" => "kit_select",
		//		"items" => [
		//			self::getEnchantedItem(Item::get(Item::IRON_AXE), [Enchantment::getEnchantment(Enchantment::TYPE_WEAPON_SHARPNESS)]),
		//			Item::get(Item::GOLDEN_APPLE, 0, 16),
		//			Item::get(Item::SPLASH_POTION, Potion::HEALING_TWO),
		//			Item::get(Item::SPLASH_POTION, Potion::HEALING_TWO),
		//			Item::get(Item::SPLASH_POTION, Potion::HEALING_TWO),
		//			Item::get(Item::SPLASH_POTION, Potion::HEALING_TWO),
		//			Item::get(Item::SPLASH_POTION, Potion::HEALING_TWO),
		//			Item::get(Item::SPLASH_POTION, Potion::HEALING_TWO),
		//			Item::get(Item::SPLASH_POTION, Potion::HEALING_TWO),
		//		],
		//		"armor" => [
		//			Item::get(Item::IRON_HELMET),
		//			Item::get(Item::IRON_CHESTPLATE),
		//			Item::get(Item::IRON_LEGGINGS),
		//			Item::get(Item::IRON_BOOTS),
		//		],
		//	],
		//];
	}

}