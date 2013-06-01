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

use TYPO3\CMS\Core\SingletonInterface;

/**
 * Description of a devlog
 *
 * @package Devlog
 * @author Georg Großberger <georg@grossberger.at>
 * @copyright 2013 by Georg Großberger
 * @license GPL v3 http://www.gnu.org/licenses/gpl-3.0.txt
 */
interface LoggerInterface extends SingletonInterface {

	/**
	 * Used by the the TYPO3 devlog API
	 *
	 * @see GeneralUtility::devlog
	 * @param array $params
	 * @return void
	 */
	public function coreCall(array $params);

	/**
	 * Log debug data
	 * Severity info can be seen as "debug"
	 *
	 * @param string $message
	 * @param string $extensionKey
	 * @param array $debugData
	 * @return void
	 */
	public function info($message, $extensionKey, array $debugData = NULL);

	/**
	 * Log a notice
	 *
	 * @param string $message
	 * @param string $extensionKey
	 * @param array $debugData
	 * @return void
	 */
	public function notice($message, $extensionKey, array $debugData = NULL);

	/**
	 * Log a warning
	 *
	 * @param string $message
	 * @param string $extensionKey
	 * @param array $debugData
	 * @return void
	 */
	public function warning($message, $extensionKey, array $debugData = NULL);

	/**
	 * Log an error
	 *
	 * @param string $message
	 * @param string $extensionKey
	 * @param array $debugData
	 * @return void
	 */
	public function error($message, $extensionKey, array $debugData = NULL);

	/**
	 * Log a fatal error
	 *
	 * @param string $message
	 * @param string $extensionKey
	 * @param array $debugData
	 * @return void
	 */
	public function fatal($message, $extensionKey, array $debugData = NULL);
}
