<?php
/*                                                                     *
 * This file is brought to you by Georg Großberger                     *
 * (c) 2013 by Georg Großberger <contact@grossberger-ge.org>           *
 *                                                                     *
 * It is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License, either version 3       *
 * of the License, or (at your option) any later version.              *
 *                                                                     */

namespace TYPO3Community\Devlog\Processors;

use TYPO3\CMS\Core\Log\LogRecord;
use TYPO3\CMS\Core\Log\Processor\AbstractProcessor;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Add a complete backtrace of the calling function
 *
 * @package Devlog
 * @author Georg Großberger <georg@grossberger.at>
 * @copyright 2013 by Georg Großberger
 * @license GPL v3 http://www.gnu.org/licenses/gpl-3.0.txt
 */
class BackTraceProcessor extends AbstractProcessor {

	/**
	 * Processes a log record and adds additional data.
	 *
	 * @param LogRecord $logRecord The log record to process
	 * @return LogRecord The processed log record with additional data
	 */
	public function processLogRecord(LogRecord $logRecord) {
		$backTrace = array_slice(debug_backtrace(), 4);
		$logRecord->addData(array('__trace' => $backTrace));
		return $logRecord;
	}
}
