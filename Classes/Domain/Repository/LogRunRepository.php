<?php
/*                                                                     *
 * This file is brought to you by Georg Großberger                     *
 * (c) 2013 by Georg Großberger <contact@grossberger-ge.org>           *
 *                                                                     *
 * It is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License, either version 3       *
 * of the License, or (at your option) any later version.              *
 *                                                                     */

namespace TYPO3Community\Devlog\Domain\Repository;

use TYPO3Community\Devlog\Domain\Model\LogRun;

/**
 * Repository of log runs
 *
 * @package Devlog
 * @author Georg Großberger <georg@grossberger.at>
 * @copyright 2013 by Georg Großberger
 * @license GPL v3 http://www.gnu.org/licenses/gpl-3.0.txt
 */
class LogRunRepository extends AbstractRepository {

	/**
	 *
	 * @var LogRun
	 */
	protected $currentRun;

	public function getOrCreateRunning() {
		if (!$this->currentRun) {
			$this->currentRun = $this->createRunObject();
		}
		return $this->currentRun;
	}

	protected function createRunObject() {
		$object = new LogRun();
		$object->initializeObject();
		return $object;
	}

	/**
	 * @return null|LogRun
	 */
	public function findLatest() {
		$row = $this->db->exec_SELECTgetSingleRow('*', $this->getTableName(), 'uid < ' . $this->currentRun->getUid(), '', 'uid DESC');
		if (!empty($row['uid'])) {
			return $this->createRunObject()->_load($row);
		}
		return NULL;
	}

	/**
	 * @param LogRun $run
	 * @return null|LogRun
	 */
	public function findNext(LogRun $run) {
		$row = $this->db->exec_SELECTgetSingleRow('*', $this->getTableName(), 'uid > ' . $run->getUid(), '', 'uid ASC');
		if (!empty($row['uid'])) {
			return $this->createRunObject()->_load($row);
		}
		return NULL;
	}

	/**
	 * @param LogRun $run
	 * @return null|LogRun
	 */
	public function findPrevious(LogRun $run) {
		$row = $this->db->exec_SELECTgetSingleRow('*', $this->getTableName(), 'uid < ' . $run->getUid(), '', 'uid DESC');
		if (!empty($row['uid'])) {
			return $this->createRunObject()->_load($row);
		}
		return NULL;
	}
}
