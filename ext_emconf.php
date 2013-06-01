<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "devlog".
 *
 * Auto generated 23-05-2013 18:05
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
	'title' => 'Extension Development Logger',
	'description' => 'Upload your extensions into the TER',
	'category' => 'misc',
	'author' => 'Georg Großberger',
	'author_email' => 'contact@grossberger-ge.org',
	'author_company' => '',
	'shy' => '',
	'priority' => '',
	'dependencies' => 'extbase,fluid,extensionmanager',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => '0',
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '0.1.0',
	'constraints' =>
	array (
		'depends' =>
		array (
			'extbase' => '6.0-6.9.99',
			'fluid' => '6.0-6.9.99',
			'typo3' => '6.0-6.9.99',
			'extensionmanager' => '6.0.0-6.9.99',
		),
		'conflicts' =>
		array (
		),
		'suggests' =>
		array (
		),
	),
	'suggests' =>
	array (
	),
	'conflicts' => '',
);

?>
