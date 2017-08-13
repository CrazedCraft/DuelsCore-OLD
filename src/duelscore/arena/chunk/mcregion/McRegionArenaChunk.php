<?php

/**
 * DuelsCore – McRegionArenaChunk.php
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
 * Created on 7/7/17 at 6:01 PM
 *
 */

namespace duelscore\arena\chunk\mcregion;

use pocketmine\level\format\LevelProvider;
use \pocketmine\level\format\mcregion\Chunk;
use pocketmine\level\format\mcregion\McRegion;
use pocketmine\nbt\tag\ByteTag;

/**
 * Extends default chunk class and adds extended functionality
 */
class McRegionArenaChunk extends Chunk {

	/**
	 * Save options for chunk
	 *
	 * @param int $x
	 * @param int $z
	 * @param $blocks
	 * @param $data
	 * @param $skyLight
	 * @param mixed $blockLight
	 * @param mixed $colors
	 * @param mixed $heightMap
	 * @param LevelProvider $provider
	 *
	 * @return McRegionArenaChunk
	 */
	public static function fromData($x, $z, $blocks, $data, $skyLight, $blockLight, $colors, $heightMap, LevelProvider $provider = null) {
		$chunk = new McRegionArenaChunk($provider instanceof LevelProvider ? $provider : McRegion::class, null);
		$chunk->provider = $provider;
		$chunk->x = $x;
		$chunk->z = $z;

		$chunk->blocks = $blocks;
		$chunk->data = $data;
		$chunk->skyLight = $skyLight;
		$chunk->blockLight = $blockLight;
		$chunk->allowUnload = false;

		$chunk->heightMap = $heightMap;
		$chunk->biomeColors = $colors;

		$chunk->nbt->TerrainGenerated = new ByteTag("TerrainGenerated", 1);
		$chunk->nbt->TerrainPopulated = new ByteTag("TerrainPopulated", 1);
		$chunk->nbt->LightPopulated = new ByteTag("LightPopulated", 1);

		return $chunk;
	}

}