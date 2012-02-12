<?php

namespace DataGrid\Columns;

/**
 * Representation of checkbox data grid column.
 *
 * @author     Roman Sklenář
 * @copyright  Copyright (c) 2009 Roman Sklenář (http://romansklenar.cz)
 * @license    New BSD License
 * @example    http://addons.nette.org/datagrid
 * @package    Nette\Extras\DataGrid
 */
class CheckboxColumn
extends NumericColumn
{
	/**
	 * Checkbox column constructor.
	 * @param string $caption column's textual caption
	 */
	public function __construct($caption = NULL)
	{
		parent::__construct($caption, 0);
		$this->getCellPrototype()->style('text-align: center');
	}

	/**
	 * Formats cell's content.
	 * @param mixed $value
	 * @param \DibiRow|array $data
	 * @return string
	 */
	public function formatContent($value, $data = NULL)
	{
		$checkbox = \Nette\Utils\Html::el('input')->type('checkbox')->disabled('disabled');
		if ($value) {
			$checkbox->checked = TRUE;
		}
		return (string)$checkbox;
	}

	/**
	 * Filter data source
	 * @param mixed $value
	 */
	public function applyFilter($value)
	{
		if (!$this->hasFilter()) {
			return;
		}

		$dataSource = $this->getDataGrid()->getDataSource();
		$value = (boolean)$value;
		if ($value) {
			$dataSource->filter($this->name, '>=', $value);
		} else {
			$dataSource->filter($this->name, array('=', 'IS NULL'), $value, 'OR');
		}
	}
}
