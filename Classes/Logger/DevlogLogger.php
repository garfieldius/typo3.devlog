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
use TYPO3\CMS\Core\Log\LogRecord;
use TYPO3\CMS\Core\Log\Writer\WriterInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
	 * @param LogRecord $record Log record
	 * @return WriterInterface $this
	 * @throws \Exception
	 */
	public function writeLog(LogRecord $record) {
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

		if (stripos($extension, '\\') !== FALSE) {
			$parts = explode('\\', $extension);
			if ($parts[0] === 'TYPO3' && $parts[1] === 'CMS') {
				$extension = $parts[2];
			} else {
				$extension = $parts[1];
			}
			$extension = GeneralUtility::camelCaseToLowerCaseUnderscored($extension);
		}

		$inserts = array(
			'crdate'   => time(),
			'severity' => $severity,
			'extkey'   => $extension,
			'msg'      => $message,
			'crmsec'   => $this->config->getCurrentRun(),
			'ip'       => GeneralUtility::getIndpEnv('REMOTE_ADDR')
		);

		if (TYPO3_MODE === 'FE' && isset($GLOBALS['TSFE'])) {
			$inserts['pid'] = $GLOBALS['TSFE']->id;
		} elseif (isset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['GLOBAL']['debugData']['pid'])) {
			$inserts['pid'] = $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['GLOBAL']['debugData']['pid'];
		}

		if (!empty($debugData)) {
			$debugData = json_encode($this->converter->convertData($debugData));
			if (strlen($debugData) < $this->config->getDumpSize()) {
				$inserts['data_var'] = $debugData;
			} else {
				$inserts['data_var'] = '{"error":{"type":"string","value":"Debug data too long"}}';
			}
		}

		if (!empty($GLOBALS['BE_USER']->user['uid'])) {
			$inserts['cruser_id'] = $GLOBALS['BE_USER']->user['uid'];
		}

		$stack = debug_backtrace();

		if (!empty($stack[$traceStart])) {
			$inserts['location'] = $stack[$traceStart]['file'];
			$inserts['line'] = $stack[$traceStart]['line'];
		}

		// "Lazy" saving in case the db connection is not yet available
		// May happen when the deprecation log is set to "devlog"
		if (!isset($GLOBALS['TYPO3_DB']) || !$GLOBALS['TYPO3_DB'] instanceof DatabaseConnection) {
			$this->backLog[] = $inserts;
			return;
		} elseif (empty($this->db)) {
			$this->db = $GLOBALS['TYPO3_DB'];
			if (!empty($this->backLog)) {
				$this->db->exec_INSERTmultipleRows('tx_devlog', array_keys($this->backLog[0]), $this->backLog);
				$this->backLog = NULL;
			}
		}
		$this->db->exec_INSERTquery('tx_devlog', $inserts);
	}
}
