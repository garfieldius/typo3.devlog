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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Simple access for easier dev-logging
 *
 * @package Devlog
 * @author Georg Großberger <georg@grossberger.at>
 * @copyright 2013 by Georg Großberger
 * @license GPL v3 http://www.gnu.org/licenses/gpl-3.0.txt
 */
class DevlogFactory {

	/**
	 * @var LoggerInterface
	 */
	private static $instance;

	/**
	 *
	 * @return LoggerInterface
	 */
	public static function getLog() {
		if (!self::$instance) {
			$logger = array_shift(explode('->', $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['devLog']['devlog']));
			self::$instance = GeneralUtility::makeInstance($logger);
		}
		return self::$instance;
	}
}
