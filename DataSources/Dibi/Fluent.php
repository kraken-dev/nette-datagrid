<?php

namespace DataGrid\DataSources\Dibi;

use DataGrid\DataSources\IDataSource,
	DataGrid\DataSources,
	dibi;

/**
 * Dibi fluent based data source
 * @author Pavel Kučera
 * @author Michael Moravec
 * @author Štěpán Svoboda
 * @author Petr Morávek
 */
class Fluent
extends DataSources\Mapped
{
	/** @var \DibiFluent Dibi fluent instance */
	private $df;

	/** @var array Fetched data */
	private $data;

	/** @var int Total data count */
	private $count;


	/**
	 * Store given dibi data fluent instance
	 * @param \DibiFluent $df
	 */
	public function __construct(\DibiFluent $df)
	{
		$this->df = $df;
	}

	/**
	 * Add filtering onto specified column
	 * @param string $column column name
	 * @param string $operation filter
	 * @param string|array $value operation mode
	 * @param string $chainType chain type (if third argument is array)
	 * @return Fluent (fluent)
	 * @throws \Nette\InvalidArgumentException
	 */
	public function filter($column, $operation = IDataSource::EQUAL, $value = NULL, $chainType = NULL)
	{
		if (!$this->hasColumn($column)) {
			throw new \Nette\InvalidArgumentException('Trying to filter data source by unknown column.');
		}

		if (is_array($operation)) {
			if ($chainType !== self::CHAIN_AND && $chainType !== self::CHAIN_OR) {
				throw new \Nette\InvalidArgumentException('Invalid chain operation type.');
			}
			$conds = array();
			foreach ($operation as $t) {
				$this->validateFilterOperation($t);
				if ($t === self::IS_NULL || $t === self::IS_NOT_NULL) {
					$conds[] = array('%n', $this->mapping[$column], $t);
				} else {
					$modifier = is_double($value) ? dibi::FLOAT : dibi::TEXT;
					if ($operation === self::LIKE || $operation === self::NOT_LIKE) {
						$value = DataSources\Utils\WildcardHelper::formatLikeStatementWildcards($value);
					}

					$conds[] = array('%n', $this->mapping[$column], $t, '%' . $modifier, $value);
				}
			}

			if ($chainType === self::CHAIN_AND) {
				foreach ($conds as $cond) {
					$this->df->where($cond);
				}
			} elseif ($chainType === self::CHAIN_OR) {
				$this->df->where('( %or )', $conds);
			}
		} else {
			$this->validateFilterOperation($operation);

			if ($operation === self::IS_NULL || $operation === self::IS_NOT_NULL) {
				$this->qb->where('%n', $this->mapping[$column], $operation);
			} else {
				$modifier = is_double($value) ? dibi::FLOAT : dibi::TEXT;
				if ($operation === self::LIKE || $operation === self::NOT_LIKE) {
					$value = DataSources\Utils\WildcardHelper::formatLikeStatementWildcards($value);
				}

				$this->df->where('%n', $this->mapping[$column], $operation, '%' . $modifier, $value);
			}
		}

		return $this;
	}

	/**
	 * Adds ordering to specified column
	 * @param string $column column name
	 * @param string $order one of ordering types
	 * @return Fluent (fluent)
	 * @throws \Nette\InvalidArgumentException
	 */
	public function sort($column, $order = IDataSource::ASCENDING)
	{
		if (!$this->hasColumn($column)) {
			throw new \Nette\InvalidArgumentException('Trying to sort data source by unknown column.');
		}

		$this->df->orderBy($this->mapping[$column], $order === self::ASCENDING ? 'ASC' : 'DESC');

		return $this;
	}

	/**
	 * Reduce the result starting from $start to have $count rows
	 * @param int $count the number of results to obtain
	 * @param int $start the offset
	 * @return Fluent (fluent)
	 * @throws \Nette\OutOfRangeException
	 */
	public function reduce($count, $start = 0)
	{
		if ($count == NULL || $count > 0) { //intentionally ==
			$this->df->limit($count == NULL ? 0 : $count);
		} else {
			throw new \Nette\OutOfRangeException;
		}

		if ($start == NULL || ($start > 0 && $start < count($this))) {
			$this->df->offset($start == NULL ? 0 : $start);
		} else {
			throw new \Nette\OutOfRangeException;
		}

		return $this;
	}

	/**
	 * Get iterator over data source items
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->fetch());
	}

	/**
	 * Fetches and returns the result data.
	 * @return array
	 */
	public function fetch()
	{
		return $this->data = $this->df->fetchAll();
	}

	/**
	 * Count items in data source
	 * @return int
	 * @todo: if there is a group by clause in the query, count it correctly
	 */
	public function count()
	{
		$query = clone $this->df;

		$query->removeClause('select')
				->removeClause('limit')
				->removeClause('offset')
				->removeClause('order by')
				->select('count(*)');

		return $this->count = (int)$query->fetchSingle();
	}

	/**
	 * Return distinct values for a selectbox filter
	 * @param string $column Column name
	 * @return array
	 */
	public function getFilterItems($column)
	{
		throw new \Nette\NotImplementedException;
	}

	/**
	 * Clone dibi fluent instance
	 */
	public function __clone()
	{
		$this->df = clone $this->df;
	}
}
