<?php
/*                                                                     *
 * This file is brought to you by Georg Großberger                     *
 * (c) 2013 by Georg Großberger <contact@grossberger-ge.org>           *
 *                                                                     *
 * It is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License, either version 3       *
 * of the License, or (at your option) any later version.              *
 *                                                                     */

namespace TYPO3Community\Devlog\Utility;

use ReflectionClass;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\DomainObject\AbstractValueObject;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Convert the debug data into an array
 *
 * @package package
 * @author Georg Großberger <georg@grossberger.at>
 * @copyright 2013 by Georg Großberger
 * @license GPL v3 http://www.gnu.org/licenses/gpl-3.0.txt
 */
class DebugDataConverter extends DebuggerUtility implements SingletonInterface {

	/**
	 * Convert data
	 *
	 * @param array $data
	 * @return array
	 */
	public function convertData(array $data) {
		self::clearState();
		return $this->convertArray($data);
	}

	/**
	 * Convert an array of values
	 *
	 * @param array $data
	 * @return array
	 */
	protected function convertArray(array $data) {
		$result = array();
		foreach ($data as $key => $variable) {
			$result[$key] = $this->convertValue($variable);
		}
		return $result;
	}

	/**
	 * Convert a value
	 * Arrays and objects are passed to their converters, primitives are just returned
	 *
	 * @param $value
	 * @return array
	 */
	protected function convertValue($value) {
		$dump = array(
			'type' => gettype($value)
		);
		switch ($dump['type']) {
			case 'array':
				$dump['type'] = 'array';
				$dump['elements'] = $this->convertArray($value);
				$dump['length'] = count($dump['elements']);
				break;

			case 'object':
				$dump = array_merge($dump, $this->convertObject($value));
				break;

			case 'resource':
				$dump['value'] = (string) $value;
				break;

			case 'double':
				$dump['type'] = 'float';
				$dump['value'] = $value;
				break;

			default:
				$dump['value'] = $value;
		}
		return $dump;
	}

	/**
	 * Convert a collection object
	 *
	 * @param $collection
	 * @return array
	 */
	protected function convertCollection($collection) {
		$data = array();

		foreach ($collection as $key => $element) {
			$data[$key] = $this->convertValue($element);
		}

		if ($collection instanceof \Iterator) {
			$collection->rewind();
		}
		return $data;
	}

	/**
	 * Convert an object
	 *
	 * @param $object
	 * @return array
	 */
	protected function convertObject($object) {
		if ($object instanceof LazyLoadingProxy) {
			$object = $object->_loadRealInstance();
		}

		$class = get_class($object);
		$doNotDescend = FALSE;
		$info = array(
			'class' => $class
		);

		if (self::isAlreadyRendered($object)) {
			$doNotDescend = TRUE;
			$info['_recursion'] = TRUE;
		} else {
			self::$renderedObjects->attach($object);
		}

		if ($object instanceof ObjectStorage || $object instanceof \Iterator || $object instanceof \ArrayObject) {
			$info['collection'] = TRUE;
			$info['elements'] = $this->convertCollection($object);
			$info['length'] = count($info['elements']);
			return $info;
		}

		$tags = array();

		if (self::isBlacklisted($object)) {
			$doNotDescend = TRUE;
			$tags[] = TRUE;
		}

		if ($object instanceof SingletonInterface) {
			$tags[] = 'singleton';
		}

		if ($object instanceof AbstractDomainObject) {
			if ($object->_isDirty()) {
				$tags[] = 'modified';
			} elseif ($object->_isNew()) {
				$tags[] = 'transient';
			} else {
				$tags[] = 'persistent';
			}
		}

		if ($object instanceof AbstractEntity) {
			$tags[] = 'entity';
		} elseif ($object instanceof AbstractValueObject) {
			$tags[] = 'valueobject';
		}

		if ($object instanceof ObjectStorage && $object->_isDirty()) {
			$tags[] = 'modified';
		}

		if (!empty($tags)) {
			$info['tags'] = $tags;
		}

		if ($doNotDescend) {
			return $info;
		}

		$classReflection = new ReflectionClass(get_class($object));
		$properties = array();

		foreach ($classReflection->getProperties() as $property) {
			if (self::isBlacklisted($property)) {
				continue;
			}
			$property->setAccessible(TRUE);
			$properties[] = array(
				'name' => $property->getName(),
				'value' => $this->convertValue($property->getValue($object))
			);
		}

		if (!empty($properties)) {
			$info['properties'] = $properties;
			$info['length'] = count($properties);
		}

		return $info;
	}
}
