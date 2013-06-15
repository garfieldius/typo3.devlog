<?php
/*                                                                     *
 * This file is brought to you by Georg Großberger                     *
 * (c) 2013 by Georg Großberger <contact@grossberger-ge.org>           *
 *                                                                     *
 * It is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License, either version 3       *
 * of the License, or (at your option) any later version.              *
 *                                                                     */

namespace TYPO3Community\Devlog\Domain\Model;

use DateTime;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Model of a TYPO3 run
 *
 * @package Devlog
 * @author Georg Großberger <georg@grossberger.at>
 * @copyright 2013 by Georg Großberger
 * @license GPL v3 http://www.gnu.org/licenses/gpl-3.0.txt
 */
class LogRun extends AbstractRecord {

	/**
	 * @var string
	 */
	protected $requestId;

	/**
	 * @var \DateTime
	 */
	protected $start;

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3Community\Devlog\Domain\Model\LogRecord>
	 */
	protected $entries;

	public function initializeObject() {
		$this->requestId = Bootstrap::getInstance()->getRequestId() ?: uniqid('', TRUE);
		$this->start = new DateTime();
		$this->entries = new ObjectStorage();
		$this->_relations = array(
			'entries' => array(
				'to' => 'TYPO3Community\Devlog\Domain\Model\LogRecord',
				'type' => '1m',
				'mappedBy' => 'run'
			)
		);
		return $this;
	}

	/**
	 * @param ObjectStorage $entries
	 */
	public function setEntries(ObjectStorage $entries) {
		$this->entries = $entries;
	}

	public function addEntry(LogRecord $entry) {
		$entry->_save();
		$this->_save();
		$this->entries->attach($entry);
	}

	/**
	 * @return ObjectStorage
	 */
	public function getEntries() {
		return $this->entries;
	}

	/**
	 * @param string $requestId
	 */
	public function setRequestId($requestId) {
		$this->requestId = $requestId;
	}

	/**
	 * @return string
	 */
	public function getRequestId() {
		return $this->requestId;
	}

	/**
	 * @param DateTime $start
	 */
	public function setStart(DateTime $start) {
		$this->start = $start;
	}

	/**
	 * @return \DateTime
	 */
	public function getStart() {
		return $this->start;
	}
}
