<?php

namespace DataGrid\Columns;

/**
 * Representation of date data grid column.
 *
 * @author     Roman Sklenář
 * @copyright  Copyright (c) 2009 Roman Sklenář (http://romansklenar.cz)
 * @license    New BSD License
 * @example    http://addons.nette.org/datagrid
 * @package    Nette\Extras\DataGrid
 */
class DateColumn
extends TextColumn
{
	/** @var string */
	public $format;


	/**
	 * Date column constructor.
	 * @param string $caption column's textual caption
	 * @param string $format date format supported by PHP strftime()
	 */
	public function __construct($caption = NULL, $format = '%x')
	{
		parent::__construct($caption);
		$this->format = $format;
	}

	/**
	 * Formats cell's content.
	 * @param mixed $value
	 * @param \DibiRow|array $data
	 * @return string
	 */
	public function formatContent($value, $data = NULL)
	{
		if ((int)$value == NULL || empty($value)) {
			return 'N/A';
		}
		$value = parent::formatContent($value, $data);

		$value = is_numeric($value) ? (int) $value : ($value instanceof \DateTime ? $value->format('U') : strtotime($value));
		return strftime($this->format, $value);
	}

	/**
	 * Applies filtering on dataset.
	 * @param mixed $value
	 */
	public function applyFilter($value)
	{
		if (!$this->hasFilter()) {
			return;
		}

		$this->getDataGrid()->getDataSource()->filter($this->name, '=', $value);
	}
}
