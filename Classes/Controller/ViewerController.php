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

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

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
	 * @var \TYPO3Community\Devlog\Data\DataRepository
	 */
	protected $data;
	/**
	 * @var \TYPO3Community\Devlog\Utility\Configuration
	 */
	protected $config;

	/**
	 * @param \TYPO3Community\Devlog\Data\DataRepository $data
	 */
	public function injectData(\TYPO3Community\Devlog\Data\DataRepository $data) {
		$this->data = $data;
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

	/**
	 * List all logged actions of a run
	 * Handle the run ID as string, otherwise a 32-bit PHP will lower it to PHP_INT_MAX
	 *
	 * @param string $run
	 * @param string $extension
	 */
	public function listAction($run = NULL, $extension = NULL) {
		$data = $this->data->getRunData($run, $extension);
		$this->view->assignMultiple($data)
					->assign('next', $this->data->findNextRunId($data['run']))
					->assign('previous', $this->data->findPreviousId($data['run']))
					->assign('allcount', $this->data->countAllRuns())
					->assign('activeExtension', $extension);
	}

	/**
	 * Execute the gc and just tell us if something happend
	 *
	 * @return string
	 */
	public function cleanAction() {
		if ($this->data->cleanTable()) {
			return 'yes';
		} else {
			return 'no';
		}
	}

	/**
	 * Load the debug data of an entry
	 * Since we store JSON in the DB, no need to issue a view rendering
	 *
	 * @param integer $debugEntry
	 * @return string
	 */
	public function detailAction($debugEntry = NULL) {
		$this->response->setHeader('Content-Type', 'application/json;charset=UTF-8');
		return $this->data->findDebugDataByEntry($debugEntry);
	}
}
