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

/**
 * Model of a single devlog entry
 *
 * @package Devlog
 * @author Georg Großberger <georg@grossberger.at>
 * @copyright 2013 by Georg Großberger
 * @license GPL v3 http://www.gnu.org/licenses/gpl-3.0.txt
 */
class LogRecord extends AbstractRecord {

	/**
	 * @var \TYPO3Community\Devlog\Domain\Model\LogRun
	 */
	protected $run;

	/**
	 * @var integer
	 */
	protected $pageId;

	/**
	 * @var \DateTime
	 */
	protected $time;

	/**
	 * @var string
	 */
	protected $message;

	/**
	 * @var string
	 */
	protected $component;

	/**
	 * @var integer
	 */
	protected $severity;

	/**
	 * @var string
	 */
	protected $debugData;

	public function initializeObject() {
		parent::initializeObject();
		$this->_relations = array(
			'run' => array(
				'to' => 'TYPO3Community\Devlog\Domain\Model\LogRun',
				'type' => 'm1',
			)
		);
	}

	/**
	 * @param string $component
	 */
	public function setComponent($component) {
		$this->component = $component;
	}

	/**
	 * @return string
	 */
	public function getComponent() {
		return $this->component;
	}

	/**
	 * @param string $debugData
	 */
	public function setDebugData($debugData) {
		$this->debugData = $debugData;
	}

	/**
	 * @return string
	 */
	public function getDebugData() {
		return $this->debugData;
	}

	/**
	 * @param string $message
	 */
	public function setMessage($message) {
		$this->message = $message;
	}

	/**
	 * @return string
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @param int $pageId
	 */
	public function setPageId($pageId) {
		$this->pageId = $pageId;
	}

	/**
	 * @return int
	 */
	public function getPageId() {
		return $this->pageId;
	}

	/**
	 * @param \TYPO3Community\Devlog\Domain\Model\LogRun $run
	 */
	public function setRun($run) {
		$this->run = $run;
	}

	/**
	 * @return \TYPO3Community\Devlog\Domain\Model\LogRun
	 */
	public function getRun() {
		return $this->run;
	}

	/**
	 * @param int $severity
	 */
	public function setSeverity($severity) {
		$this->severity = $severity;
	}

	/**
	 * @return int
	 */
	public function getSeverity() {
		return $this->severity;
	}

	/**
	 * @param \DateTime $time
	 */
	public function setTime($time) {
		$this->time = $time;
	}

	/**
	 * @return \DateTime
	 */
	public function getTime() {
		return $this->time;
	}
}
