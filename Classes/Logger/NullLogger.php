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

/**
 * A do nothing devlog for not having to bother about development or production context
 *
 * @package Devlog
 * @author Georg Großberger <georg@grossberger.at>
 * @copyright 2013 by Georg Großberger
 * @license GPL v3 http://www.gnu.org/licenses/gpl-3.0.txt
 */
class NullLogger implements LoggerInterface {

	/**
	 * Used by the the TYPO3 devlog API
	 *
	 * @see GeneralUtility::devlog
	 * @param array $params
	 * @return void
	 */
	public function coreCall(array $params) {
		// Does nothing
	}

	/**
	 * Log debug data
	 * Severity info can be seen as "debug"
	 *
	 * @param string $message
	 * @param string $extensionKey
	 * @param array $debugData
	 * @return void
	 */
	public function info($message, $extensionKey, array $debugData = NULL) {
		// Does nothing
	}

	/**
	 * Log a notice
	 *
	 * @param string $message
	 * @param string $extensionKey
	 * @param array $debugData
	 * @return void
	 */
	public function notice($message, $extensionKey, array $debugData = NULL) {
		// Does nothing
	}

	/**
	 * Log a warning
	 *
	 * @param string $message
	 * @param string $extensionKey
	 * @param array $debugData
	 * @return void
	 */
	public function warning($message, $extensionKey, array $debugData = NULL) {
		// Does nothing
	}

	/**
	 * Log an error
	 *
	 * @param string $message
	 * @param string $extensionKey
	 * @param array $debugData
	 * @return void
	 */
	public function error($message, $extensionKey, array $debugData = NULL) {
		// Does nothing
	}

	/**
	 * Log a fatal error
	 *
	 * @param string $message
	 * @param string $extensionKey
	 * @param array $debugData
	 * @return void
	 */
	public function fatal($message, $extensionKey, array $debugData = NULL) {
		// Does nothing
	}
}
