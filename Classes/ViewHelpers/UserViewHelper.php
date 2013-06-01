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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\Utility\IconUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Make the user UID more visually apealing
 *
 * @package Devlog
 * @author Georg Großberger <georg@grossberger.at>
 * @copyright 2013 by Georg Großberger
 * @license GPL v3 http://www.gnu.org/licenses/gpl-3.0.txt
 */
class UserViewHelper extends AbstractViewHelper{

	static protected $users = array();

	/**
	 * @param integer $userId
	 * @return string
	 */
	public function render($userId = NULL) {
		if (is_null($userId)) {
			$userId = (int) $this->renderChildren();
		}

		if (empty($userId)) {
			return '';
		}

		if (!isset(self::$users[$userId])) {
			self::$users[$userId] = BackendUtility::getRecord('be_users', $userId);
		}
		$user = self::$users[$userId];

		$content = $user['username'] . ' ' . IconUtility::getSpriteIcon('status-user-' . ($user['admin'] ? 'admin' : 'backend'));
		return $content;
	}
}
