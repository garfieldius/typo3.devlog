<?php
/*                                                                     *
 * This file is brought to you by Georg Großberger                     *
 * (c) 2013 by Georg Großberger <contact@grossberger-ge.org>           *
 *                                                                     *
 * It is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License, either version 3       *
 * of the License, or (at your option) any later version.              *
 *                                                                     */

namespace TYPO3Community\Devlog\Logger;

use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\Writer\WriterInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Community\Devlog\Domain\Model\LogRecord;
use TYPO3Community\Devlog\Domain\Model\LogRun;
use TYPO3Community\Devlog\Utility\Configuration;

/**
 * Log to the devlog table
 *
 * @package Devlog
 * @author Georg Großberger <georg@grossberger.at>
 * @copyright 2013 by Georg Großberger
 * @license GPL v3 http://www.gnu.org/licenses/gpl-3.0.txt
 */
class DevlogLogger implements WriterInterface {

	/**
	 *
	 * @var LogRun
	 */
	protected $run;

	/**
	 *
	 * @var array
	 */
	protected $backLog = array();

	/**
	 * @var \TYPO3Community\Devlog\Utility\DebugDataConverter
	 */
	protected $converter;

	/**
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected $db;

	/**
	 * @var Configuration
	 */
	protected $config;

	public function __construct() {
		// Extbase may not be ready yet, so we do it ourselves
		$this->converter = GeneralUtility::makeInstance('TYPO3Community\Devlog\Utility\DebugDataConverter');
		$this->config = GeneralUtility::makeInstance('TYPO3Community\Devlog\Utility\Configuration');
	}

	/**
	 * Writes the log record
	 *
	 * @param \TYPO3\CMS\Core\Log\LogRecord $record Log record
	 * @return WriterInterface $this
	 * @throws \Exception
	 */
	public function writeLog(\TYPO3\CMS\Core\Log\LogRecord $record) {
		$this->storeLog($record->getMessage(), $record->getComponent(), $record->getLevel(), $record->getData(), 4);
		return $this;
	}

	/**
	 * @param string $message
	 * @param string $extension
	 * @param integer $severity
	 * @param mixed $debugData
	 * @param integer $traceStart
	 */
	protected function storeLog($message, $extension, $severity, $debugData, $traceStart = 2) {

		if ($this->config->isExcludedKey($extension) || $severity > $this->config->getMinLogLevel()) {
			return;
		}

		$record = new LogRecord();
		$record->initializeObject();
		$record->setMessage($message);
		$record->setComponent($extension);
		$record->setSeverity($severity);
		$record->setTime(new \DateTime());
		$record->setPid(0);

		if (TYPO3_MODE === 'FE' && isset($GLOBALS['TSFE'])) {
			$record->setPageId($GLOBALS['TSFE']->id);
		} elseif (isset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['GLOBAL']['debugData']['pid'])) {
			$record->setPageId($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['GLOBAL']['debugData']['pid']);
		}

		if (!empty($debugData)) {
			$debugData = json_encode($this->converter->convertData($debugData));
			if (strlen($debugData) > $this->config->getDumpSize()) {
				$debugData = '{"error":{"type":"string","value":"Debug data too long"}}';
			}
			$record->setDebugData($debugData);
		}

		if (!$this->run) {
			$this->run = GeneralUtility::makeInstance('TYPO3Community\Devlog\Domain\Repository\LogRunRepository')->getOrCreateRunning();
		}

		$record->setRun($this->run);
		$this->run->addEntry($record);
	}

	/**
	 * Used by the the old TYPO3 devlog API
	 *
	 * @see GeneralUtility::devlog
	 * @param array $params
	 * @return void
	 */
	public function coreCall(array $params) {
		$this->storeLog($params['msg'], $params['extKey'], LogLevel::DEBUG - ($params['severity'] * 2), $params['dataVar'], 4);
	}
}
