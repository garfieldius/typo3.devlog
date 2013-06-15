<?php
/*                                                                     *
 * This file is brought to you by Georg Großberger                     *
 * (c) 2013 by Georg Großberger <contact@grossberger-ge.org>           *
 *                                                                     *
 * It is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License, either version 3       *
 * of the License, or (at your option) any later version.              *
 *                                                                     */

namespace TYPO3Community\Devlog\Domain\Model;

use DateTime;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Base for a non-extbase domain model
 *
 * @package Devlog
 * @author Georg Großberger <georg@grossberger.at>
 * @copyright 2013 by Georg Großberger
 * @license GPL v3 http://www.gnu.org/licenses/gpl-3.0.txt
 */
class AbstractRecord extends AbstractEntity {

	protected $_relations = array();

	/**
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	private $_db;

	function __construct() {
		$this->_db = $GLOBALS['TYPO3_DB'];
		$this->initializeObject();
	}

	public function _load($properties, $noRelations = FALSE) {

		if (!is_array($properties) && is_numeric($properties)){
			$properties = $this->_db->exec_SELECTgetSingleRow('*', $this->getTableName(), 'uid=' . (int) $properties);
		}

		if (!empty($properties)) {

			$this->uid = (int) $properties['uid'];
			unset($properties['uid']);

			foreach ($properties as $name => $value) {

				if (isset($this->_relations[$name]) && !$noRelations) {

					$config = $this->_relations[$name];

					if ($config['type'] == '1m') {
						$value = new ObjectStorage();
						$targetClass = $config['to'];
						$res = $this->_db->exec_SELECTquery('uid', $this->getTableName($targetClass), $config['mappedBy'] . ' = ' . $this->uid);

						while ($row = $this->_db->sql_fetch_assoc($res)) {
							$object = GeneralUtility::makeInstance($targetClass);
							$object->_load($row['uid']);
							$value->attach($object);
						}
					} elseif ($config['type'] == 'm1') {
						$value = GeneralUtility::makeInstance($config['to'])->_load($value, TRUE);
					}
				}
				$this->_setProperty($name, $value);
			}
		}

		$this->_memorizeCleanState();
		return $this;
	}

	public function _delete() {
		if (!$this->_isNew()) {
			$this->_db->exec_DELETEquery($this->getTableName(), 'uid = ' . $this->getUid());
			$this->uid = NULL;
		}
		return $this;
	}

	public function _getProperties() {
		$properties = array();
		foreach (get_object_vars($this) as $name => $value) {

			if ($value instanceof DateTime) {
				$value = $value->format('U');
			} elseif ($value instanceof ObjectStorage) {
				$value = $value->count();
			} elseif ($value instanceof AbstractRecord) {
				if ($value->_isNew()) {
					$value->_save();
				}
				$value = $value->getUid();
			}

			if (!empty($value) && substr($name, 0, 1) !== '_' && $name !== 'uid') {
				$properties[$name] = $value;
			}
		}
		return $properties;
	}

	protected function getTableName($class = NULL) {
		if (!$class) {
			$class = get_class($this);
		}
		return 'tx_' . strtolower(implode('_', array_slice(explode('\\', $class), 1)));
	}

	public function _save() {
		$fields = $this->_getProperties();
		$table  = $this->getTableName();

		if ($this->_isNew()) {
			$this->_db->exec_INSERTquery($table, $fields);
			$this->uid = (int) $this->_db->sql_insert_id();
		} else {
			$this->_db->exec_UPDATEquery($table, 'uid = ' . $this->getUid(), $fields);
		}
		return $this;
	}
}
