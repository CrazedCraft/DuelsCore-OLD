<?php

namespace duels\tasks;

use pocketmine\scheduler\Task;

class Restart extends Task {

	protected $callable;

	protected $args;

	public function __construct(callable $callable, array $args = []) {
		$this->callable = $callable;
		$this->args = $args;
		$this->args[] = $this;
	}

	public function onRun($tick) {
		call_user_func_array($this->callable, $this->args);
	}

}
