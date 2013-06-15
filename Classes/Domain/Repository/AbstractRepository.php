<?php
/*                                                                     *
 * This file is brought to you by Georg Großberger                     *
 * (c) 2013 by Georg Großberger <contact@grossberger-ge.org>           *
 *                                                                     *
 * It is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License, either version 3       *
 * of the License, or (at your option) any later version.              *
 *                                                                     */

namespace TYPO3Community\Devlog\Domain\Repository;

use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Exception\NotImplementedException;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Extbase\Persistence\RepositoryInterface;
use TYPO3Community\Devlog\Domain\Model\AbstractRecord;

/**
 * Repository of log runs
 *
 * @package Devlog
 * @author Georg Großberger <georg@grossberger.at>
 * @copyright 2013 by Georg Großberger
 * @license GPL v3 http://www.gnu.org/licenses/gpl-3.0.txt
 */
class AbstractRepository implements RepositoryInterface, SingletonInterface {

	/**
	 * @var DatabaseConnection
	 */
	protected $db;

	function __construct() {
		$this->db = $GLOBALS['TYPO3_DB'];
	}

	protected function save(AbstractRecord $record) {
		$record->_delete();
	}

	protected function delete(AbstractRecord $record) {
		$record->_delete();
	}

	/**
	 * Adds an object to this repository.
	 *
	 * @param object $object The object to add
	 * @return void
	 * @api
	 */
	public function add($object) {
		$this->save($object);
	}

	/**
	 * Removes an object from this repository.
	 *
	 * @param object $object The object to remove
	 * @return void
	 * @api
	 */
	public function remove($object) {
		$this->delete($object);
	}

	/**
	 * Replaces an existing object with the same identifier by the given object
	 *
	 * @param object $modifiedObject The modified object
	 * @api
	 */
	public function update($modifiedObject) {
		$this->save($modifiedObject);
	}

	/**
	 * Returns all objects of this repository.
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array The query result
	 * @api
	 */
	public function findAll() {
		$found = array();
		$res = $this->db->exec_SELECTquery('*', $this->getTableName(), '');
		$model = $this->getModelName();

		while ($row = $this->db->sql_fetch_assoc($res)) {
			$object = GeneralUtility::makeInstance($model);
			$object->_load($row);
		}

		return $found;
	}

	/**
	 * Returns the total number objects of this repository.
	 *
	 * @return integer The object count
	 * @api
	 */
	public function countAll() {
		return $this->db->exec_SELECTcountRows('uid', $this->getTableName());
	}

	/**
	 * Removes all objects of this repository as if remove() was called for
	 * all of them.
	 *
	 * @return void
	 * @api
	 */
	public function removeAll() {
		$this->db->exec_DELETEquery($this->getTableName(), '');
	}

	/**
	 * Finds an object matching the given identifier.
	 *
	 * @param integer $uid The identifier of the object to find
	 * @return object The matching object if found, otherwise NULL
	 * @api
	 */
	public function findByUid($uid) {
		$object = GeneralUtility::makeInstance($this->getModelName());
		$object->_load($uid);
		if ($object->_isNew()) {
			return NULL;
		} else {
			return $object;
		}
	}

	/**
	 * Finds an object matching the given identifier.
	 *
	 * @param mixed $identifier The identifier of the object to find
	 * @return object The matching object if found, otherwise NULL
	 * @api
	 */
	public function findByIdentifier($identifier) {
		return $this->findByUid($identifier);
	}

	/**
	 * Sets the property names to order the result by per default.
	 * Expected like this:
	 * array(
	 * 'foo' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
	 * 'bar' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING
	 * )
	 *
	 * @param array $defaultOrderings The property names to order by
	 * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception\NotImplementedException
	 * @return void
	 * @api
	 */
	public function setDefaultOrderings(array $defaultOrderings) {
		throw new NotImplementedException('Not available in this context');
	}

	/**
	 * Sets the default query settings to be used in this repository
	 *
	 * @param QuerySettingsInterface $defaultQuerySettings The query settings to be used by default
	 * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception\NotImplementedException
	 * @return void
	 * @api
	 */
	public function setDefaultQuerySettings(QuerySettingsInterface $defaultQuerySettings) {
		throw new NotImplementedException('Not available in this context');
	}

	/**
	 * Returns a query for objects of this repository
	 *
	 * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception\NotImplementedException
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
	 * @api
	 */
	public function createQuery() {
		throw new NotImplementedException('Not available in this context');
	}

	protected function getTableName($class = NULL) {
		$class = $this->getModelName($class);
		return 'tx_' . strtolower(implode('_', array_slice(explode('\\', $class), 1)));
	}

	protected function getModelName($class = NULL) {
		if (!$class) {
			$class = get_class($this);
		}
		$class = str_replace('\\Repository\\', '\\Model\\', $class);
		$class = str_replace('Repository', '', $class);
		$class = ltrim($class, '\\');
		return $class;
	}
}
