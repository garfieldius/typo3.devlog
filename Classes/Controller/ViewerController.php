<?php
/*                                                                     *
 * This file is brought to you by Georg Großberger                     *
 * (c) 2013 by Georg Großberger <contact@grossberger-ge.org>           *
 *                                                                     *
 * It is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License, either version 3       *
 * of the License, or (at your option) any later version.              *
 *                                                                     */

namespace TYPO3Community\Devlog\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3Community\Devlog\Domain\Model\LogRecord;

/**
 * Backend Controller
 *
 * @package Devlog
 * @author Georg Großberger <georg@grossberger.at>
 * @copyright 2013 by Georg Großberger
 * @license GPL v3 http://www.gnu.org/licenses/gpl-3.0.txt
 */
class ViewerController extends ActionController {

	/**
	 *
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected $db;

	/**
	 *
	 * @var \TYPO3Community\Devlog\Domain\Repository\LogRunRepository
	 */
	protected $runRepository;

	/**
	 * @var \TYPO3Community\Devlog\Utility\Configuration
	 */
	protected $config;

	/**
	 * @param \TYPO3Community\Devlog\Domain\Repository\LogRunRepository $runRepository
	 */
	public function injectRunRepository(\TYPO3Community\Devlog\Domain\Repository\LogRunRepository $runRepository) {
		$this->runRepository = $runRepository;
	}

	/**
	 * @param \TYPO3Community\Devlog\Utility\Configuration $config
	 */
	public function injectConfig(\TYPO3Community\Devlog\Utility\Configuration $config) {
		$this->config = $config;
	}

	protected function initializeView(ViewInterface $view) {
		$view->assign('config', $this->config->getAll());
	}

	protected function initializeAction() {
		parent::initializeAction();
		$this->db = $GLOBALS['TYPO3_DB'];
	}

	/**
	 * List all logged actions of a run
	 * We cannot use extbase to load this, because it cannot handle the refs to the
	 * records without some proper TCA
	 *
	 * @param integer $run
	 */
	public function listAction($run = NULL) {

		if (!$run) {
			$run = $this->runRepository->findLatest();
		} else {
			$run = $this->runRepository->findByUid($run);
		}

		$this->view->assign('run', $run);
		$this->view->assign('allcount', $this->runRepository->countAll());

		if ($run) {
			$this->view->assign('next', $this->runRepository->findNext($run));
			$this->view->assign('previous', $this->runRepository->findPrevious($run));
		}
	}

	/**
	 * Execute the gc and just tell us if something happend
	 *
	 * @return string
	 */
	public function cleanAction() {
		if ($this->runRepository->countAll() > $this->config->getMaxSavedRuns()) {
			$row = $this->db->exec_SELECTgetSingleRow(
				'uid',
				'tx_devlog_domain_model_logrun',
				'',
				'',
				'uid DESC',
				$this->config->getMaxSavedRuns()
			);

			if (!empty($row['uid'])) {
				$this->db->exec_DELETEquery(
					'tx_devlog_domain_model_logrun',
					'uid > ' . $row['uid']
				);

				$this->db->exec_DELETEquery(
					'tx_devlog_domain_model_logrecord',
					'run NOT IN (SELECT uid FROM tx_devlog_domain_model_logrun)'
				);
				return 'yes';
			}
		}
		return 'no';
	}

	/**
	 * Load the debug data of an entry
	 * Since we store JSON in the DB, no need to issue a view rendering
	 *
	 * @param integer $recordId
	 * @return string
	 */
	public function detailAction($recordId) {
		$this->response->setHeader('Content-Type', 'application/json;charset=UTF-8');
		if ($recordId) {
			$record = GeneralUtility::makeInstance('TYPO3Community\Devlog\Domain\Repository\LogRecordRepository')->findByUid($recordId);

			if ($record instanceof LogRecord) {
				return $record->getDebugData();
			}
		}
		return '{"error":"No debug data found"}';
	}
}
