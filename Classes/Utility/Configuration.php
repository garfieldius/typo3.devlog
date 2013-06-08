<?php
/*                                                                     *
 * This file is brought to you by Georg Großberger                     *
 * (c) 2013 by Georg Großberger <contact@grossberger-ge.org>           *
 *                                                                     *
 * It is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License, either version 3       *
 * of the License, or (at your option) any later version.              *
 *                                                                     */

namespace TYPO3Community\Devlog\Utility;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Quick access to config params
 *
 * @package Devlog
 * @author Georg Großberger <georg@grossberger.at>
 * @copyright 2013 by Georg Großberger
 * @license GPL v3 http://www.gnu.org/licenses/gpl-3.0.txt
 */
class Configuration implements SingletonInterface {

	/**
	 * Actually a string, because it has to be compatible with 32-bit PHPs
	 *
	 * @var integer
	 */
	protected $currentRun = 0;

	/**
	 * @var integer
	 */
	protected $maxSavedRuns = 100;

	/**
	 * @var integer
	 */
	protected $dumpSize = 4294967295;

	/**
	 * @var integer
	 */
	protected $minLogLevel = 7;

	/**
	 * @var string
	 */
	protected $excludeKeys = '';

	public function __construct() {
		if (!empty($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['devlog'])) {
			$config = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['devlog']);
			if (is_array($config)) {
				foreach ($config as $key => $value) {
					$this->$key = $value;
				}
			}
		}
		$this->currentRun = str_replace('.', '', (string) microtime(TRUE));
	}

	/**
	 * @return integer
	 */
	public function getCurrentRun() {
		return $this->currentRun;
	}

	/**
	 * @return integer
	 */
	public function getDumpSize() {
		return $this->dumpSize;
	}

	/**
	 * @return string
	 */
	public function getExcludeKeys() {
		return $this->excludeKeys;
	}

	/**
	 * @return integer
	 */
	public function getMaxSavedRuns() {
		return $this->maxSavedRuns;
	}

	/**
	 * @return integer
	 */
	public function getMinLogLevel() {
		return $this->minLogLevel;
	}

	/**
	 * @param string $key
	 * @return boolean
	 */
	public function isExcludedKey($key) {
		return GeneralUtility::inList($this->excludeKeys, $key);
	}

	/**
	 * @return array
	 */
	public function getAll() {
		return array(
			'currentRun'   => $this->currentRun,
			'maxSavedRuns' => $this->maxSavedRuns,
			'dumpSize'     => $this->dumpSize,
			'minLogLevel'  => $this->minLogLevel,
			'excludeKeys'  => $this->excludeKeys
		);
	}
}
