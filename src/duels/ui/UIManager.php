<?php

/**
 * DuelsCore – UIManager.php
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
 * Created on 12/9/17 at 6:45 PM
 *
 */

namespace duels\ui;

use core\language\LanguageUtils;
use duels\Main;
use duels\ui\elements\DuelRequestKitSelectionButton;
use duels\ui\elements\PlayKitSelectionButton;
use duels\ui\windows\DefaultServerSelectionForm;
use duels\ui\windows\DuelRequestKitSelectionForm;
use duels\ui\windows\PlayKitSelectionForm;
use pocketmine\customUI\CustomUI;

class UIManager {

	/** @var Main */
	private $plugin = null;

	/** @var CustomUI[] */
	private $formPool = [];

	public function __construct(Main $plugin) {
		$this->plugin = $plugin;

		$this->registerDefaults();
	}

	protected function registerDefaults() {
		$this->registerForm(new PlayKitSelectionForm(LanguageUtils::translateColors("&l&eSelect a kit to play"), PlayKitSelectionButton::class), PlayKitSelectionForm::FORM_UI_ID);
		$this->registerForm(new DuelRequestKitSelectionForm(LanguageUtils::translateColors("&l&eSelect a kit"), DuelRequestKitSelectionButton::class), DuelRequestKitSelectionForm::FORM_UI_ID);
		$this->registerForm(new DefaultServerSelectionForm(), DefaultServerSelectionForm::FORM_UI_ID);
	}

	/**
	 * @param string $id
	 *
	 * @return null|CustomUI
	 */
	public function getForm(string $id) {
		if($this->formExists($id)) {
			return clone $this->formPool[$id];
		}
		return null;
	}

	/**
	 * @param CustomUI $form
	 * @param string $id
	 * @param bool $overwrite
	 *
	 * @return bool
	 * @throws \ErrorException
	 */
	public function registerForm(CustomUI $form, string $id, bool $overwrite = false) {
		if(!$this->formExists($id) or $overwrite) {
			$this->formPool[$id] = $form;
			return true;
		}

		throw new \ErrorException("Attempted to overwrite existing form!");
	}

	/**
	 * @param string $id
	 *
	 * @return bool
	 */
	public function formExists(string $id) {
		return isset($this->formPool[$id]) and $this->formPool[$id] instanceof CustomUI;
	}

}