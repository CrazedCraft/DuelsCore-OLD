<?php

declare(strict_types=1);

namespace duels\duel\request;

interface DuelRequestState {

	const STATE_ALL = 0; // receive requests from all players
	const STATE_FRIENDS = 1; // only receive requests from friends
	const STATE_NONE = 2; // don't receive any requests

}