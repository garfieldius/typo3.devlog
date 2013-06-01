<?php
/*                                                                     *
 * This file is brought to you by Georg Großberger                     *
 * (c) 2013 by Georg Großberger <contact@grossberger-ge.org>           *
 *                                                                     *
 * It is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License, either version 3       *
 * of the License, or (at your option) any later version.              *
 *                                                                     */

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['devLog'][$_EXTKEY] =
	'&TYPO3Community\Devlog\Logger\\' .
	\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3Community\Devlog\Utility\Configuration')->getLogger() .
	'Logger->coreCall';
