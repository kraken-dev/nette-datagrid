<?php
namespace DataGrid\DataSources\Dibi;

use Nette, DataGrid;

/**
 * An dibi data source for DataGrid
 * @author Lopo <lopo@losys.sk>
 */
class Dibi
extends DataGrid\DataSources\DataSource
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

	public function __construct(\DibiDataSource $dds)
	{
		$this->source=$dds;
	}

	public function filter($column, $operation=self::EQUAL, $value=NULL, $chainType=NULL)
	{
		$s=clone $this->source;
		return $s->where("$column $operation $value");
	}

	public function sort($column, $order=self::ASCENDING)
	{
		if (!$this->hasColumn($column)) {
			throw new \InvalidArgumentException;
			}
		$s=clone $this->source;
		return $s->orderBy($column, $order);
	}

	public function reduce($count, $start=0)
	{
		$s=clone $this->source;
		$s->applyLimit($count, $start);
	}

	public function getColumns()
	{
		$s=clone $this->source;
		$row=$s->select('*')->applyLimit(1)->fetch();
		return array_keys((array)$row);
	}

	public function hasColumn($name)
	{
		return in_array($name, $this->getColumns());
	}

	public function getFilterItems($column)
	{
		throw new \NotImplementedException;
	}

	public function getIterator()
	{
		$s=clone $this->source;
		return $s->getIterator();
	}

	public function count()
	{
		$s=clone $this->source;
		return $s->count();
	}
}
