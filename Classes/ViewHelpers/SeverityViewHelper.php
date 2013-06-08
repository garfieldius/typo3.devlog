<?php
/*                                                                     *
 * This file is brought to you by Georg Großberger                     *
 * (c) 2013 by Georg Großberger <contact@grossberger-ge.org>           *
 *                                                                     *
 * It is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License, either version 3       *
 * of the License, or (at your option) any later version.              *
 *                                                                     */

namespace TYPO3Community\Devlog\ViewHelpers;

use TYPO3\CMS\Backend\Utility\IconUtility;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Create an icon out of the severity int
 *
 * @package Devlog
 * @author Georg Großberger <georg@grossberger.at>
 * @copyright 2013 by Georg Großberger
 * @license GPL v3 http://www.gnu.org/licenses/gpl-3.0.txt
 */
class SeverityViewHelper extends AbstractViewHelper {

	/**
	 * @param integer $severity
	 * @return string
	 */
	public function render($severity = NULL) {
		if (is_null($severity)) {
			$severity = (int) $this->renderChildren();
		}

		switch ($severity) {
			case LogLevel::DEBUG:
				$icon = 'status-dialog-information';
				break;

			case LogLevel::INFO:
			case LogLevel::NOTICE:
				$icon = 'status-dialog-notification';
				break;

			case LogLevel::WARNING:
				$icon = 'status-dialog-warning';
				break;

			case LogLevel::ERROR:
			case LogLevel::CRITICAL:
				$icon = 'status-dialog-error';
				break;

			case LogLevel::ALERT:
			case LogLevel::EMERGENCY:
				$icon = 'status-status-permission-denied';
				break;

			default:
				$icon = 'status-status-icon-missing';
		}

		return IconUtility::getSpriteIcon($icon);
	}
}
