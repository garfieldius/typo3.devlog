<?php
/*                                                                     *
 * This file is brought to you by Georg Großberger                     *
 * (c) 2013 by Georg Großberger <contact@grossberger-ge.org>           *
 *                                                                     *
 * It is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License, either version 3       *
 * of the License, or (at your option) any later version.              *
 *                                                                     */

namespace TYPO3Community\Devlog\Data;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3Community\Devlog\Utility\Configuration;

/**
 * Repository for accessing data
 *
 * @package Devlog
 * @author Georg Großberger <georg@grossberger.at>
 * @copyright 2013 by Georg Großberger
 * @license GPL v3 http://www.gnu.org/licenses/gpl-3.0.txt
 */
class DataRepository {

	/**
	 * @var DatabaseConnection
	 */
	protected $db;

	/**
	 * @var Configuration
	 */
	protected $config;

	public function __construct() {
		$this->db = $GLOBALS['TYPO3_DB'];
		$this->config = GeneralUtility::makeInstance('TYPO3Community\Devlog\Utility\Configuration');
	}

	/**
	 * @return integer
	 */
	public function countAllRuns() {
		return $this->db->exec_SELECTcountRows('DISTINCT crmsec', 'tx_devlog', '');
	}

	/**
	 * @return boolean
	 */
	public function cleanTable() {
		if ($this->countAllRuns() > $this->config->getMaxSavedRuns()) {
			$res = $this->db->exec_SELECTquery('crmsec', 'tx_devlog', '', 'crmsec', 'crmsec DESC', $this->config->getMaxSavedRuns() . ',1');
			if ($this->db->sql_num_rows($res)) {
				$row = $this->db->sql_fetch_assoc($res);
				return (boolean) $this->db->exec_DELETEquery('tx_devlog', 'crmsec <= ' . $row['crmsec']);
			}
		}
		return FALSE;
	}

	/**
	 * @param integer $entry
	 * @return string
	 */
	public function findDebugDataByEntry($entry) {
		$result = $this->db->exec_SELECTgetSingleRow('data_var', 'tx_devlog', 'uid=' . (int) $entry);
		if (!empty($result['data_var'])) {
			return (string) $result['data_var'];
		} else {
			return '{}';
		}
	}

	/**
	 *
	 * @return string
	 */
	public function findLatestRunId() {
		$result = $this->db->exec_SELECTgetSingleRow('crmsec', 'tx_devlog', 'crmsec < ' . $this->config->getCurrentRun(), 'crmsec', 'crmsec DESC');
		return $result['crmsec'];
	}

	/**
	 * @param null|string $runId
	 * @param null|string $extKey
	 * @return array
	 */
	public function getRunData($runId = NULL, $extKey = NULL) {
		if (!$runId) {
			$runId = $this->findLatestRunId();
		}

		$where = 'crmsec = ' . $this->db->fullQuoteStr($runId, 'tx_devlog');

		if (is_string($extKey)) {
			$where .= ' AND extkey = ' . $this->db->fullQuoteStr($extKey, 'tx_devlog');
		}

		$extensions = array();
		$users = array();
		$data = array(
			'run' => $runId,
			'entries' => array()
		);

		$res = $this->db->exec_SELECTquery(
			'uid,pid,crdate,cruser_id AS cruser,severity,extkey,ip,msg,location AS file,line,IF(data_var IS NOT NULL, 1, 0) AS data',
			'tx_devlog',
			$where,
			'',
			'crdate ASC, uid ASC'
		);
		while ($row = $this->db->sql_fetch_assoc($res)) {
			if (!empty($row['extkey']) && !isset($extensions[$row['extkey']])) {
				$extensions[$row['extkey']] = TRUE;
			}

			if (!empty($row['cruser_id']) && !isset($users[$row['cruser_id']])) {
				$users[$row['cruser_id']] = BackendUtility::getRecord('be_users', $row['cruser_id'], 'username,email');
			}

			$data['entries'][] = $row;
		}

		$data['extensions'] = $extensions;
		$data['users'] = $users;
		return $data;
	}

	public function findNextRunId($current) {
		return $this->findSingle($current, '>');
	}

	/**
	 * @param integer $current
	 * @return null|integer
	 */
	public function findPreviousId($current) {
		return $this->findSingle($current, '<');
	}

	protected function findSingle($current, $operator) {
		$quotedCurrent = $this->db->fullQuoteStr($current, 'tx_devlog');
		$quotedRunning = $this->db->fullQuoteStr($this->config->getCurrentRun(), 'tx_devlog');
		$row = $this->db->exec_SELECTgetSingleRow(
			'crmsec',
			'tx_devlog',
			'crmsec ' . $operator . ' ' . $quotedCurrent . ' AND crmsec != ' . $quotedRunning,
			'crmsec',
			'crmsec DESC'
		);

		if (!empty($row['crmsec'])) {
			return $row['crmsec'];
		}
		return NULL;
	}
}
