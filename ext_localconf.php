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
	'TYPO3Community\Devlog\Logger\DevlogLogger->coreCall';

$processors = array(
	'TYPO3Community\Devlog\Processors\UserDataProcessor' => array(),
	'TYPO3Community\Devlog\Processors\BackTraceProcessor' => array()
);

if (!empty($GLOBALS['TYPO3_CONF_VARS']['LOG']['processorConfiguration'][\TYPO3\CMS\Core\Log\LogLevel::DEBUG])) {
	$GLOBALS['TYPO3_CONF_VARS']['LOG']['processorConfiguration'][\TYPO3\CMS\Core\Log\LogLevel::DEBUG] = array_merge(
		$GLOBALS['TYPO3_CONF_VARS']['LOG']['processorConfiguration'][\TYPO3\CMS\Core\Log\LogLevel::DEBUG],
		$processors
	);
} else {
	$GLOBALS['TYPO3_CONF_VARS']['LOG']['processorConfiguration'][\TYPO3\CMS\Core\Log\LogLevel::DEBUG] = $processors;
}
unset($processors);

$GLOBALS['TYPO3_CONF_VARS']['LOG']['writerConfiguration'][
	\TYPO3\CMS\Core\Log\LogLevel::DEBUG
][
	'TYPO3Community\Devlog\Logger\DevlogLogger'
] = array();
