<?php

namespace DataGrid\DataSources\PHPArray;

/**
 * An array data source for DataGrid
 * @author Michael Moravec
 */
class PHPArray
extends \DataGrid\DataSources\DataSource
{
	/** @var array */
	private $items;

	/** @var array */
	private $source;

	/** @var array */
	private $filters;

	/** @var array */
	private $sorting;

	/** @var array */
	private $reducing;


	/**
	 * @param array $items
	 * @throws \Nette\InvalidArgumentException
	 */
	public function __construct(array $items)
	{
		if (empty($items)) {
			throw new \Nette\InvalidArgumentException('Empty array given');
		}

		$this->items = $this->source = $items;
	}

	/**
	 */
	public function filter($column, $operation = self::EQUAL, $value = NULL, $chainType = NULL)
	{
		throw new \Nette\NotImplementedException;
	}

	/**
	 * @param string $column
	 * @throws \Nette\InvalidArgumentException
	 */
	public function sort($column, $order = self::ASCENDING)
	{
		if (!$this->hasColumn($column)) {
			throw new \Nette\InvalidArgumentException;
		}
		usort($this->items, function ($a, $b) use ($column, $order) {
			return $order === \DataGrid\DataSources\IDataSource::DESCENDING ? -strcmp($a[$column], $b[$column]) : strcmp($a[$column], $b[$column]);
		});
	}

	/**
	 * @param int $count
	 * @param int $start
	 */
	public function reduce($count, $start = 0)
	{
		$this->items = array_slice($this->items, $start, $count);
	}

	/**
	 * @return array
	 */
	public function getColumns()
	{
		return array_keys(reset($this->source));
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasColumn($name)
	{
		return array_key_exists($name, reset($this->source));
	}

	/**
	 */
	public function getFilterItems($column)
	{
		throw new \Nette\NotImplementedException;
	}

	/**
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->items);
	}

	/**
	 * @return int
	 */
	public function count()
	{
		return count($this->items);
	}
}
