<?php

namespace duels\npc;

class Skin {

	public static function add($dir, $skin, $name) {
		if(!is_dir($dir)) {
			@mkdir($dir);
		}
		$file = fopen($dir . DIRECTORY_SEPARATOR . $name . ".skin", "w");
		fwrite($file, "skin: " . zlib_encode($skin, ZLIB_ENCODING_DEFLATE, 9) . "\n\r\n");
		fwrite($file, "name: " . $name . "\n\r\n");
		fclose($file);
	}

	public static function get($dir, $name) {
		if(!is_file($dir . $name . ".skin")) return null;
		$array = [];
		if(file_exists($dir . DIRECTORY_SEPARATOR . $name . ".skin") and strlen($content = file_get_contents($dir . DIRECTORY_SEPARATOR . $name . ".skin")) > 0) {
			foreach(explode("\n\r\n", $content) as $line) {
				$line = trim($line);
				if($line === "" or $line{0} === "#") {
					continue;
				}

				$t = explode(": ", $line);
				if(count($t) < 2) {
					continue;
				}

				$key = trim(array_shift($t));
				$value = trim(implode(": ", $t));

				if($key === "skin") {
					$value = zlib_decode((string)$value);
				}

				if($value === "") {
					continue;
				}

				$array[$key] = $value;
			}
			return $array;
		}
	}

	public function exists($dir, $name) {
		return is_file($dir . $name . ".skin");
	}

}
