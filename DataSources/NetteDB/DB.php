<?php

namespace DataGrid\DataSources\NetteDB;

use DataGrid\DataSources\IDataSource;
/**
 * @author Dušan Jakub, FIT VUT Brno
 */
class DB
extends \DataGrid\DataSources\Mapped
{
	/** @var \Nette\Database\Table\Selection */
	private $selection;

	/** @var array Fetched data */
	private $data;

	/** @var int Total data count */
	private $count;


	/**
	 * Store given selection
	 * @param \Nette\Database\Table\Selection $sel
	 */
	public function __construct(\Nette\Database\Table\Selection $sel)
	{
		$this->selection = $sel;
	}

	/**
	 * @param array $mapping
	 */
	public function setMapping(array $mapping)
	{
		parent::setMapping($mapping);
		foreach ($mapping as $k => $m) {
			$this->selection->select("$m AS `$k`");
		}
	}

	/**
	 * Add filtering onto specified column
	 * @param string $column column name
	 * @param string $operation filter
	 * @param string|array $value operation mode
	 * @param string $chainType chain type (if third argument is array)
	 * @return DB (fluent)
	 * @throws \Nette\InvalidArgumentException
	 */
	public function filter($column, $operation = IDataSource::EQUAL, $value = NULL, $chainType = NULL)
	{
		$col=$column;
		if ($this->hasColumn($column)) {
			$col = $this->mapping[$column];
		}

		if (is_array($operation)) {
			if ($chainType !== self::CHAIN_AND && $chainType !== self::CHAIN_OR) {
				throw new \Nette\InvalidArgumentException('Invalid chain operation type.');
			}
		} else {
			$operation = array($operation);
		}

		if (empty($operation)) {
			throw new \Nette\InvalidArgumentException('Operation cannot be empty.');
		}

		$conds = array();
		$values = array();
		foreach ($operation as $o) {
			$this->validateFilterOperation($o);

			$c = "$col $o";
			if ($o !== self::IS_NULL && $o !== self::IS_NOT_NULL) {
				$c .= ' ?';

				$values[] = ($o === self::LIKE || $o === self::NOT_LIKE)
					? \DataGrid\DataSources\Utils\WildcardHelper::formatLikeStatementWildcards($value)
					: $value;
			}

			$conds[] = $c;
		}

		$conds = implode(" ( $chainType ) ", $conds); // "(cond1) OR (cond2) ..."  -- outer braces missing for now
		$this->selection->where("( $conds )", $values);

		return $this;
	}

	/**
	 * Adds ordering to specified column
	 * @param string $column column name
	 * @param string $order one of ordering types
	 * @return DB (fluent)
	 */
	public function sort($column, $order = IDataSource::ASCENDING)
	{
		if (!$this->hasColumn($column)) {
			$this->selection->order($column . ' ' . ($order === self::ASCENDING ? 'ASC' : 'DESC'));
		}
		else {
			$this->selection->order($this->mapping[$column] . ' ' . ($order === self::ASCENDING ? 'ASC' : 'DESC'));
		}

		return $this;
	}

	/**
	 * Reduce the result starting from $start to have $count rows
	 * @param int $count the number of results to obtain
	 * @param int $start the offset
	 * @return DB (fluent)
	 * @throws \Nette\OutOfRangeException
	 */
	public function reduce($count, $start = 0)
	{
		// Delibearately skipping check agains count($this)

		if ($count === NULL) {
			$count = 0;
		}
		if ($start === NULL) {
			$start = 0;
		}

		if ($start < 0 || $count < 0) {
			throw new \Nette\OutOfRangeException;
		}

		$this->selection->limit($count, $start);
		return $this;
	}

	/**
	 * Get iterator over data source items
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return $this->selection;
	}

	/**
	 * Fetches and returns the result data.
	 * @return array
	 */
	public function fetch()
	{
		throw $this->selection->fetch();
		//return $this->data = $this->df->fetchAll();
	}

	/**
	 * Count items in data source
	 * @return int
	 */
	public function count()
	{
		$query = clone $this->selection;
		$this->count = $query->count('*');

		return $this->count;
	}

	/**
	 * Return distinct values for a selectbox filter
	 * @param string $column Column name
	 * @return array
	 */
	public function getFilterItems($column)
	{
		$query = clone $this->selection;
		return $query->select($column)->group($column)->fetchPairs($column, $column);
	}

	/**
	 * Clone instance
	 */
	public function __clone()
	{
		$this->selection = clone $this->selection;
	}
}
